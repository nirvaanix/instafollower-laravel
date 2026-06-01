<?php

namespace App\Http\Controllers;

use App\Models\FollowerLog;
use App\Models\InstagramAccount;
use App\Models\User;
use App\Services\InstagramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InstagramController extends Controller
{
    public function __construct(
        protected InstagramService $instagram
    ) {}

    /**
     * Show the user's dashboard with all connected Instagram accounts.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $accounts = $user->instagramAccounts()->orderByDesc('followers_count')->get();

        return view('dashboard', [
            'user'     => $user,
            'accounts' => $accounts,
        ]);
    }

    /**
     * Redirect user to Meta OAuth authorization screen.
     */
    public function redirect()
    {
        $authUrl = $this->instagram->getAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handle the OAuth callback from Meta.
     * Exchanges code → token → fetches profile → creates/updates user + IG account → logs in.
     */
    public function callback(Request $request)
    {
        // Check for errors from Meta
        if ($request->has('error')) {
            Log::error('Instagram OAuth error', [
                'error'       => $request->get('error'),
                'reason'      => $request->get('error_reason'),
                'description' => $request->get('error_description'),
            ]);

            return redirect('/')->with('error', 'Instagram authorization was denied. Please try again.');
        }

        $code = $request->get('code');

        if (!$code) {
            return redirect('/')->with('error', 'No authorization code received from Instagram.');
        }

        // Run the full OAuth pipeline
        $data = $this->instagram->handleCallback($code);

        if (!$data) {
            return redirect('/')->with('error', 'Failed to connect to Instagram. Please ensure you have a Business or Creator account connected to a Facebook Page.');
        }

        // Find or create the user
        $user = Auth::user();

        if (!$user) {
            // Check if an IG account with this instagram_id already exists
            $existingAccount = InstagramAccount::where('instagram_id', $data['instagram_id'])->first();

            if ($existingAccount) {
                $user = $existingAccount->user;
            } else {
                // Create a new user
                $user = User::create([
                    'name'     => $data['instagram_username'] ?? 'Instagram User',
                    'email'    => $data['instagram_id'] . '@instagram.local',
                    'password' => bcrypt(Str::random(32)),
                ]);
            }
        }

        // Create or update the Instagram account
        $account = InstagramAccount::updateOrCreate(
            ['instagram_id' => $data['instagram_id']],
            [
                'user_id'             => $user->id,
                'username'            => $data['instagram_username'],
                'profile_picture_url' => $data['profile_picture_url'],
                'bio'                 => $data['bio'],
                'followers_count'     => $data['followers_count'],
                'media_count'         => $data['media_count'],
                'access_token'        => $data['access_token'],
                'token_expires_at'    => now()->addDays(60), // Long-lived token ~60 days
                'connected_at'        => now(),
                'last_sync'           => now(),
                'is_active'           => true,
            ]
        );

        // Log the initial follower count
        FollowerLog::create([
            'instagram_account_id' => $account->id,
            'followers_count'      => $data['followers_count'],
            'recorded_at'          => now(),
        ]);

        // Log the user in
        Auth::login($user);

        // Redirect to their public profile page
        return redirect('/' . $account->username)->with('success', 'Instagram connected successfully!');
    }

    /**
     * Show the public profile page for a given username.
     * Anyone can view this — no login required.
     */
    public function profile(string $username)
    {
        $account = InstagramAccount::where('username', $username)
            ->where('is_active', true)
            ->first();

        if (!$account) {
            abort(404);
        }

        $isOwner = Auth::check() && Auth::id() === $account->user_id;

        // Get recent follower history for sparkline (last 30 entries)
        $followerHistory = $account->followerLogs()
            ->orderByDesc('recorded_at')
            ->limit(30)
            ->get()
            ->reverse()
            ->values();

        return view('profile', [
            'account'         => $account,
            'isOwner'         => $isOwner,
            'followerHistory' => $followerHistory,
        ]);
    }

    /**
     * Disconnect a specific Instagram account.
     */
    public function disconnect(Request $request, InstagramAccount $account)
    {
        $user = Auth::user();

        // Ensure the user owns this account
        if (!$user || $account->user_id !== $user->id) {
            abort(403);
        }

        $account->update([
            'is_active'    => false,
            'access_token' => '',
        ]);

        // If user has no more active accounts, log them out
        if ($user->activeInstagramAccounts()->count() === 0) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('success', 'Instagram disconnected.');
        }

        return redirect('/dashboard')->with('success', '@' . $account->username . ' disconnected.');
    }
}

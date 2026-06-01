<?php

namespace App\Http\Controllers;

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
     * Redirect user to Meta OAuth authorization screen.
     */
    public function redirect()
    {
        $authUrl = $this->instagram->getAuthUrl();

        return redirect()->away($authUrl);
    }

    /**
     * Handle the OAuth callback from Meta.
     * Exchanges code → token → fetches profile → creates/updates user → logs in.
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

        // Find existing user by instagram_id, or create a new one
        $user = User::where('instagram_id', $data['instagram_id'])->first();

        if ($user) {
            // Update existing user
            $user->update([
                'instagram_username'  => $data['instagram_username'],
                'access_token'        => $data['access_token'],
                'followers_count'     => $data['followers_count'],
                'media_count'         => $data['media_count'],
                'profile_picture_url' => $data['profile_picture_url'],
                'bio'                 => $data['bio'],
                'connected_at'        => $user->connected_at ?? now(),
                'last_sync'           => now(),
            ]);
        } else {
            // Create a new user
            $user = User::create([
                'name'                => $data['instagram_username'] ?? 'Instagram User',
                'email'               => $data['instagram_id'] . '@instagram.local',
                'password'            => bcrypt(Str::random(32)),
                'instagram_id'        => $data['instagram_id'],
                'instagram_username'  => $data['instagram_username'],
                'access_token'        => $data['access_token'],
                'followers_count'     => $data['followers_count'],
                'media_count'         => $data['media_count'],
                'profile_picture_url' => $data['profile_picture_url'],
                'bio'                 => $data['bio'],
                'connected_at'        => now(),
                'last_sync'           => now(),
            ]);
        }

        // Log the user in
        Auth::login($user);

        // Redirect to their public profile page
        return redirect('/' . $user->instagram_username)->with('success', 'Instagram connected successfully!');
    }

    /**
     * Show the public profile page for a given username.
     */
    public function profile(string $username)
    {
        $profileUser = User::where('instagram_username', $username)->first();

        if (!$profileUser) {
            abort(404);
        }

        $isOwner = Auth::check() && Auth::id() === $profileUser->id;

        return view('profile', [
            'profileUser' => $profileUser,
            'isOwner'     => $isOwner,
        ]);
    }

    /**
     * Disconnect Instagram and log out.
     */
    public function disconnect(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'instagram_id'        => null,
                'instagram_username'  => null,
                'access_token'        => null,
                'followers_count'     => 0,
                'media_count'         => 0,
                'profile_picture_url' => null,
                'bio'                 => null,
                'connected_at'        => null,
                'last_sync'           => null,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Instagram disconnected.');
    }
}

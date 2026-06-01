<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstagramService
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $redirectUri;
    protected string $graphBaseUrl = 'https://graph.facebook.com/v25.0';

    public function __construct()
    {
        $this->clientId = config('services.instagram.client_id');
        $this->clientSecret = config('services.instagram.client_secret');
        $this->redirectUri = config('services.instagram.redirect_uri');
    }

    /**
     * Build the Meta OAuth authorization URL.
     * Uses Facebook Login for Business (Instagram Graph API).
     */
    public function getAuthUrl(): string
    {
        $params = http_build_query([
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUri,
            'scope'         => 'instagram_basic,pages_show_list',
            'response_type' => 'code',
            'state'         => csrf_token(),
        ]);

        return "https://www.facebook.com/v25.0/dialog/oauth?{$params}";
    }

    /**
     * Exchange the authorization code for a short-lived access token.
     */
    public function getAccessToken(string $code): ?string
    {
        $response = Http::post("{$this->graphBaseUrl}/oauth/access_token", [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'code'          => $code,
        ]);

        if ($response->failed()) {
            Log::error('Instagram: Failed to exchange code for token', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }

        return $response->json('access_token');
    }

    /**
     * Exchange a short-lived token for a long-lived token (~60 days).
     */
    public function getLongLivedToken(string $shortLivedToken): ?string
    {
        $response = Http::get("{$this->graphBaseUrl}/oauth/access_token", [
            'grant_type'    => 'fb_exchange_token',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'fb_exchange_token' => $shortLivedToken,
        ]);

        if ($response->failed()) {
            Log::error('Instagram: Failed to get long-lived token', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }

        return $response->json('access_token');
    }

    /**
     * Get the Facebook Pages connected to the user.
     * Returns the first page's ID, or null.
     */
    public function getFirstFacebookPageId(string $token): ?string
    {
        $response = Http::get("{$this->graphBaseUrl}/me/accounts", [
            'access_token' => $token,
        ]);

        if ($response->failed()) {
            Log::error('Instagram: Failed to get Facebook pages', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }

        $pages = $response->json('data');

        if (empty($pages)) {
            Log::warning('Instagram: No Facebook pages found for this user');
            return null;
        }

        return $pages[0]['id'] ?? null;
    }

    /**
     * Get the Instagram Business Account ID linked to a Facebook Page.
     */
    public function getInstagramBusinessAccountId(string $pageId, string $token): ?string
    {
        $response = Http::get("{$this->graphBaseUrl}/{$pageId}", [
            'fields'       => 'instagram_business_account',
            'access_token' => $token,
        ]);

        if ($response->failed()) {
            Log::error('Instagram: Failed to get Instagram business account', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }

        return $response->json('instagram_business_account.id');
    }

    /**
     * Get the Instagram profile info including follower count.
     */
    public function getInstagramProfile(string $igUserId, string $token): ?array
    {
        $response = Http::get("{$this->graphBaseUrl}/{$igUserId}", [
            'fields'       => 'id,username,followers_count,media_count,profile_picture_url,biography',
            'access_token' => $token,
        ]);

        if ($response->failed()) {
            Log::error('Instagram: Failed to get Instagram profile', [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);
            return null;
        }

        return $response->json();
    }

    /**
     * Convenience: Run the full flow from authorization code to profile data.
     * Returns an array with all relevant data, or null on failure at any step.
     */
    public function handleCallback(string $code): ?array
    {
        // Step 1: Exchange code for short-lived token
        $shortToken = $this->getAccessToken($code);
        if (!$shortToken) {
            return null;
        }

        // Step 2: Exchange for long-lived token
        $longToken = $this->getLongLivedToken($shortToken);
        if (!$longToken) {
            // Fall back to short token if long-lived exchange fails
            $longToken = $shortToken;
        }

        // Step 3: Get Facebook Pages
        $pageId = $this->getFirstFacebookPageId($longToken);
        if (!$pageId) {
            return null;
        }

        // Step 4: Get Instagram Business Account ID
        $igAccountId = $this->getInstagramBusinessAccountId($pageId, $longToken);
        if (!$igAccountId) {
            return null;
        }

        // Step 5: Get Instagram Profile
        $profile = $this->getInstagramProfile($igAccountId, $longToken);
        if (!$profile) {
            return null;
        }

        return [
            'access_token'        => $longToken,
            'instagram_id'        => $profile['id'] ?? null,
            'instagram_username'  => $profile['username'] ?? null,
            'followers_count'     => $profile['followers_count'] ?? 0,
            'media_count'         => $profile['media_count'] ?? 0,
            'profile_picture_url' => $profile['profile_picture_url'] ?? null,
            'bio'                 => $profile['biography'] ?? null,
        ];
    }
}

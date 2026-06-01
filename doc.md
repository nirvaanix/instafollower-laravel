# Instagram Follower System Documentation

## How the System Works

This system is an IoT Follower Counter SaaS designed to show a user's Instagram follower count on a physical NodeMCU 7-segment display.

---

## Stage 1: Instagram OAuth + Follower Count Display

### Overview
Users click "Continue with Instagram" → authorize via Meta OAuth → system fetches their follower count → displays it on the homepage and exposes it via the Device API.

### Prerequisites
1. **Instagram Account**: Must be a **Creator** or **Business** account (personal accounts are NOT supported by the Graph API).
2. **Facebook Page**: The Instagram account must be connected to a Facebook Page.
3. **Meta App**: You need a Meta App at [developers.facebook.com](https://developers.facebook.com).

---

### Setup Guide

#### 1. Create a Meta App
1. Go to [developers.facebook.com/apps](https://developers.facebook.com/apps)
2. Click **Create App** → Select **Business** type
3. Fill in the app name (e.g., "InstaFollower") and create it

#### 2. Add Instagram Product
1. In the App Dashboard, go to **Add Products**
2. Click **Set Up** on **Facebook Login for Business**
3. Under **Settings**, add the Valid OAuth Redirect URI:
   ```
   http://localhost:8000/auth/instagram/callback
   ```

#### 3. Get Credentials
1. Go to **App Settings → Basic**
2. Copy the **App ID** and **App Secret**
3. Paste them into your `.env` file:
   ```env
   INSTAGRAM_CLIENT_ID=your_app_id_here
   INSTAGRAM_CLIENT_SECRET=your_app_secret_here
   INSTAGRAM_REDIRECT_URI=http://localhost:8000/auth/instagram/callback
   ```

#### 4. Required Permissions
The app requests these Graph API permissions:
- `instagram_basic` — Read Instagram profile info & follower count
- `pages_show_list` — List Facebook Pages connected to the user
- `pages_read_engagement` — Read Instagram Business Account linked to a Page

#### 5. Run the App
```bash
php artisan serve
```
Then visit: `http://localhost:8000`

---

### Authentication Flow (Technical)

```
User clicks "Continue with Instagram"
       ↓
Redirect to: https://www.facebook.com/v22.0/dialog/oauth
       ↓
User logs in & grants permissions
       ↓
Meta redirects to: /auth/instagram/callback?code=ABC123
       ↓
Backend exchanges code for short-lived access token
  POST https://graph.facebook.com/v22.0/oauth/access_token
       ↓
Backend exchanges short-lived token for long-lived token (~60 days)
  GET https://graph.facebook.com/v22.0/oauth/access_token?grant_type=fb_exchange_token
       ↓
Backend fetches Facebook Pages
  GET https://graph.facebook.com/v22.0/me/accounts
       ↓
Backend fetches Instagram Business Account ID
  GET https://graph.facebook.com/v22.0/{page-id}?fields=instagram_business_account
       ↓
Backend fetches Instagram Profile + Follower Count
  GET https://graph.facebook.com/v22.0/{ig-user-id}?fields=id,username,followers_count,media_count,profile_picture_url
       ↓
User record created/updated in database
       ↓
User logged in → Redirected to homepage with live data
```

---

### File Structure

| File | Purpose |
|------|---------|
| `app/Services/InstagramService.php` | Core Instagram Graph API logic (OAuth, token exchange, profile fetch) |
| `app/Http/Controllers/InstagramController.php` | Handles redirect, callback, disconnect actions |
| `routes/web.php` | Web routes for OAuth flow |
| `routes/api.php` | API routes for device communication |
| `config/services.php` | Instagram credentials config |
| `resources/views/welcome.blade.php` | Homepage with connected/disconnected states |
| `resources/views/layouts/app.blade.php` | Master layout |
| `public/css/app.css` | Full design system and styles |

---

### API Endpoints

#### Web Routes
| Method | URL | Description |
|--------|-----|-------------|
| GET | `/` | Homepage (shows follower count if connected) |
| GET | `/auth/instagram/redirect` | Redirects to Meta OAuth |
| GET | `/auth/instagram/callback` | Handles OAuth callback |
| POST | `/auth/instagram/disconnect` | Disconnects Instagram |

#### Device API
| Method | URL | Headers | Description |
|--------|-----|---------|-------------|
| GET | `/api/device/stats` | `X-DEVICE-ID`, `X-DEVICE-SECRET` | Returns `{"followers": 9845}` |

---

### Database Schema

The `users` table includes these Instagram-specific fields:

| Column | Type | Description |
|--------|------|-------------|
| `instagram_id` | VARCHAR(100) | Instagram Business Account ID |
| `instagram_username` | VARCHAR(255) | Instagram username |
| `access_token` | TEXT | Long-lived access token (encrypted) |
| `followers_count` | BIGINT | Current follower count |
| `connected_at` | TIMESTAMP | When Instagram was first connected |
| `last_sync` | TIMESTAMP | When follower count was last updated |
| `device_id` | VARCHAR | Unique device identifier (e.g., DEV-A1B2C3) |
| `device_secret` | VARCHAR | Secret key for device authentication |

---

### Security

- Access tokens and device secrets are **never exposed** in the frontend
- The `User` model hides `access_token` and `device_secret` during JSON serialization
- HTTPS should be used in production for all API requests
- Passwords are never received from Instagram — a random password is generated for internal use
- CSRF protection is enabled on all POST routes

---

### Error Handling

| Scenario | User Sees |
|----------|-----------|
| User denies OAuth permissions | "Instagram authorization was denied. Please try again." |
| No authorization code received | "No authorization code received from Instagram." |
| No Facebook Page / not a Business account | "Failed to connect to Instagram. Please ensure you have a Business or Creator account connected to a Facebook Page." |
| Token expired | User needs to click "Continue with Instagram" again |

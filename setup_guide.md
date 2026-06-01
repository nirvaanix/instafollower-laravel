# 🚀 InstaFollower Setup Guide — Step by Step

> [!IMPORTANT]
> Follow these steps **in order**. You need a **Facebook account** and an **Instagram Business or Creator account** linked to a Facebook Page.

---

## ✅ Step 0: Prerequisites (Do This First)

Before anything, make sure you have:

1. **A Facebook account** (personal one is fine)
2. **An Instagram account** switched to **Business** or **Creator** mode
3. **A Facebook Page** connected to that Instagram account

### How to switch Instagram to Business Account:
1. Open Instagram app → go to your **Profile**
2. Tap **☰ Menu** (top right) → **Settings and privacy**
3. Scroll down → **Account type and tools** → **Switch to professional account**
4. Choose **Business** or **Creator** → Follow prompts
5. When asked, **connect it to your Facebook Page**

> [!NOTE]
> If you don't have a Facebook Page, create one at [facebook.com/pages/create](https://www.facebook.com/pages/create). It can be a simple page — name doesn't matter. Just link it to your Instagram.

---

## ✅ Step 1: Create a Meta Developer Account

1. Go to **[developers.facebook.com](https://developers.facebook.com/)**
2. Click **"Get Started"** (top right)
3. Log in with your Facebook account
4. Accept the terms → Click **"Register"**
5. Choose **"Developer"** when asked your role
6. Done! You're now a Meta Developer.

---

## ✅ Step 2: Create a New App

1. Go to **[developers.facebook.com/apps](https://developers.facebook.com/apps/)**
2. Click **"Create App"**
3. You'll see options — choose:
   - **App Type**: Select **"Business"**
   - **App Name**: Type `InstaFollower` (or any name you like)
   - **Contact Email**: Your email
4. Click **"Create App"**
5. You may be asked to verify with a password — enter it

> [!TIP]
> If you see "Select an app type" with options like Consumer, Business, Gaming — pick **Business**.

---

## ✅ Step 3: Get Your App ID and App Secret

After creating the app:

1. In the left sidebar, click **"App Settings"** → **"Basic"**
2. You'll see:
   - **App ID**: A long number like `123456789012345`
   - **App Secret**: Click **"Show"** (you'll need to enter your password)

### Now paste these into your `.env` file:

Open this file in your code editor: `c:\laragon\www\instafollower\.env`

Find these lines (near the bottom, lines 67-68):
```
INSTAGRAM_CLIENT_ID=
INSTAGRAM_CLIENT_SECRET=
```

Replace them with your actual values:
```
INSTAGRAM_CLIENT_ID=123456789012345
INSTAGRAM_CLIENT_SECRET=abc123def456ghi789
```

> [!CAUTION]
> **Never share your App Secret with anyone.** It's like a password.

---

## ✅ Step 4: Add Facebook Login for Business

1. In your Meta App Dashboard, look at the left sidebar
2. Find **"Add Product"** (or look for a **+ Add Product** button on the dashboard)
3. Find **"Facebook Login for Business"** → Click **"Set Up"**
4. Now in the left sidebar, click **"Facebook Login for Business"** → **"Settings"**
5. In **"Valid OAuth Redirect URIs"**, add:
   ```
   http://localhost:8000/auth/instagram/callback
   ```
6. Click **"Save Changes"**

---

## ✅ Step 5: Add Permissions

1. In the left sidebar, go to **"App Review"** → **"Permissions and Features"**
2. Search for and **request** these permissions:
   - `instagram_basic` → Click **"Get Advanced Access"**
   - `pages_show_list` → Click **"Get Advanced Access"**

> [!NOTE]
> In **Development Mode** (which you're in now), these permissions work automatically for **you** (the app owner) and any test users you add. You don't need App Review approval to test.

---

## ✅ Step 6: Add Yourself as a Test User (Optional)

If the OAuth doesn't work, you may need to add yourself as a tester:

1. Left sidebar → **"App Roles"** → **"Roles"**
2. Click **"Add People"**
3. Add your own Facebook account as **"Administrator"** or **"Tester"**

---

## ✅ Step 7: Test It!

1. Make sure your Laravel server is running:
   ```
   php artisan serve
   ```
2. Open **http://localhost:8000** in your browser
3. Click **"Continue with Instagram"**
4. You should see the Facebook login screen (NOT "Invalid app ID" anymore)
5. Log in and grant permissions
6. You'll be redirected to **http://localhost:8000/your_username** with your real follower count! 🎉

---

## ❌ Common Problems & Fixes

### "Invalid app ID"
→ Your `INSTAGRAM_CLIENT_ID` in `.env` is empty or wrong. Double-check Step 3.

### "URL blocked" on Facebook
→ You didn't add the redirect URI in Step 4. Go back and add `http://localhost:8000/auth/instagram/callback`.

### "Error validating application"
→ Your `INSTAGRAM_CLIENT_SECRET` is wrong. Go to App Settings → Basic → copy it again.

### "No Facebook pages found"
→ Your Instagram account isn't linked to a Facebook Page. Go to Step 0 and link it.

### "Failed to get Instagram business account"
→ Your Instagram is still a Personal account. Switch to Business/Creator (Step 0).

---

## 📝 Quick Checklist

- [ ] Facebook account ✓
- [ ] Instagram switched to Business/Creator ✓
- [ ] Instagram linked to a Facebook Page ✓
- [ ] Meta Developer account created ✓
- [ ] App created on Meta Developer portal ✓
- [ ] **App ID** pasted in `.env` as `INSTAGRAM_CLIENT_ID` ✓
- [ ] **App Secret** pasted in `.env` as `INSTAGRAM_CLIENT_SECRET` ✓
- [ ] Facebook Login for Business product added ✓
- [ ] Redirect URI `http://localhost:8000/auth/instagram/callback` added ✓
- [ ] `instagram_basic` + `pages_show_list` permissions enabled ✓
- [ ] Test the app at `http://localhost:8000` ✓

---

> [!TIP]
> **After everything works**, to make this a public SaaS where **anyone** (not just you) can connect, you'll need to submit your app for **App Review** on Meta and switch to **Live Mode**. But for now, test mode is perfect for development.

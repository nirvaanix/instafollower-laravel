Instagram Connect System Documentation

Overview

This document explains the complete Instagram authentication and integration flow used in the IoT Follower Counter SaaS.

The goal is simple:

1. User clicks Continue with Instagram
2. User authorizes access
3. System receives an access token
4. System fetches Instagram account data
5. Follower count is stored
6. NodeMCU device displays the count

---

User Experience Flow

Create Account
      ↓
Continue with Instagram
      ↓
Instagram Authorization
      ↓
Permissions Granted
      ↓
Instagram Connected
      ↓
Follower Count Sync
      ↓
Device Display Updated

The user never sees:

- Access Tokens
- OAuth Codes
- Graph API Requests
- Technical Configuration

Everything happens automatically.

---

Account Requirements

Instagram Graph API requires:

- Instagram Creator Account
  OR
- Instagram Business Account

Additionally:

- Facebook Page must be connected
- User must authorize the application

Personal Instagram accounts are not supported by the standard Graph API integration flow.

---

Continue with Instagram Button

Frontend Button:

<button>
Continue with Instagram
</button>

User clicks the button.

Backend generates an OAuth URL.

---

OAuth Authorization Flow

User is redirected to Meta Authorization Screen.

Example Flow:

User
 ↓
Instagram Login
 ↓
Permission Screen
 ↓
Allow Access
 ↓
Redirect Back

Redirect Example:

https://yourdomain.com/auth/instagram/callback

---

Authorization Code

After approval Meta sends:

https://yourdomain.com/auth/instagram/callback?code=ABC123XYZ

Example:

code = ABC123XYZ

This code is temporary.

It is exchanged for an Access Token.

---

Access Token Exchange

Backend sends:

POST
/oauth/access_token

Required Parameters:

client_id
client_secret
redirect_uri
code

Meta Response:

{
  "access_token": "EAABxxxxxxxxxxxx"
}

The access token represents the user's authorization.

Passwords are never received or stored.

---

Long Lived Access Token

Convert short token into long-lived token.

Short Token
      ↓
Exchange
      ↓
Long Lived Token

Benefits:

- Fewer re-logins
- Better user experience
- Reduced support requests

Store long-lived tokens in database.

---

Database Structure

Users Table

CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    email VARCHAR(255),

    instagram_id VARCHAR(100),
    instagram_username VARCHAR(255),

    access_token TEXT,

    followers_count BIGINT DEFAULT 0,

    connected_at TIMESTAMP,
    last_sync TIMESTAMP
);

---

Getting Facebook Pages

First request:

GET /me/accounts

Response:

{
  "data": [
    {
      "id": "123456789"
    }
  ]
}

This provides the connected Facebook Page.

---

Getting Instagram Business Account ID

Request:

GET /{page-id}
?fields=instagram_business_account

Example Response:

{
  "instagram_business_account": {
    "id": "17841400000000000"
  }
}

Store this ID.

---

Instagram Account Information

Available profile data may include:

Instagram ID
Username
Account Type
Biography
Profile Picture
Website
Media Count

Store relevant information for dashboard display.

---

Follower Count Sync

Scheduled background process:

Cron Job
      ↓
Graph API
      ↓
Follower Count
      ↓
Database Update

Example:

Current Count = 9845

Database:

followers_count = 9845

---

Device Communication

Each device receives:

Device ID
Device Secret

Example:

DEV-A1B2C3

SECRET-XYZ123

---

Device API

Request:

GET /api/device/stats

Headers:

X-DEVICE-ID: DEV-A1B2C3
X-DEVICE-SECRET: SECRET-XYZ123

Response:

{
  "followers": 9845
}

---

NodeMCU Display Output

Display:

0009845

Follower increases:

0009846
0009847
0009848

Display updates automatically.

---

Dashboard Features

Connected Instagram

Username
Connected Status
Last Sync
Follower Count

Example:

@sid_vaniya

Connected

Followers:
9845

Last Sync:
2 Minutes Ago

---

Recommended Sync Interval

For most users:

1 Minute
5 Minutes
10 Minutes

Recommended:

5 Minutes

Benefits:

- Lower API usage
- Lower server costs
- Better scalability

---

Error Handling

Token Expired

Instagram Connection Expired

Action:

Reconnect Instagram

---

Instagram Disconnected

No Access Available

Action:

Reconnect Account

---

API Failure

Temporary Sync Error

System retries automatically.

---

Security

Never expose:

Client Secret
Access Token
Device Secret

Always store encrypted.

Use HTTPS for all API requests.

---

Future Features

The same authentication system can later support:

Instagram Followers

Instagram Posts

Instagram Reels

Instagram Engagement

Instagram Reach

Instagram Impressions

Instagram Analytics

Instagram Growth Tracking

Instagram Milestone Alerts

---

Complete Architecture

User
 ↓
Continue with Instagram
 ↓
Meta OAuth
 ↓
Authorization Code
 ↓
Access Token
 ↓
Database
 ↓
Follower Sync Worker
 ↓
Followers Count
 ↓
Device API
 ↓
NodeMCU
 ↓
7 Segment Display

End Goal:

Instagram Account
       ↓
Cloud Backend
       ↓
Follower Count
       ↓
Physical Counter Device
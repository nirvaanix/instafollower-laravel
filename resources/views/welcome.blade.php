@extends('layouts.app')

@section('title', 'InstaFollower — See Your Instagram Followers')

@section('content')
<div class="container">

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="alert alert-error" id="flash-alert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success" id="flash-alert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="hero">
        <div class="hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
            Free &middot; Instant &middot; Powered by Instagram Graph API
        </div>

        <h1 class="hero-title">
            Your Instagram<br><span class="text-gradient">Followers</span>, One Link Away
        </h1>
        <p class="hero-subtitle">
            Connect your Instagram account and get a shareable link that displays your real-time follower count — powered entirely by the official Instagram Graph API.
        </p>

        <div class="hero-cta-area">
            <a href="{{ route('instagram.redirect') }}" class="btn btn-primary btn-cta" id="btn-connect-instagram">
                <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
                Continue with Instagram
            </a>
            <p class="hero-cta-note">Requires a Creator or Business account</p>
        </div>

        {{-- Demo URL preview --}}
        <div class="url-preview glass-card">
            <div class="url-preview-label">Your profile link will look like</div>
            <div class="url-preview-url">
                <span class="url-dim">localhost:8000/</span><span class="url-highlight">your_username</span>
            </div>
            <div class="url-preview-response">
                <span class="url-arrow">→</span>
                <span class="url-response-text">follower count, bio, profile picture...</span>
            </div>
        </div>
    </section>

    {{-- How It Works --}}
    <section class="features-section">
        <h2 class="section-title">How It <span class="text-gradient">Works</span></h2>
        <div class="grid">
            <div class="glass-card feature-card">
                <div class="feature-step">01</div>
                <div class="feature-icon" style="color: #f09433;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                </div>
                <h3 class="feature-title">Authenticate</h3>
                <p class="feature-desc">Sign in securely with your Instagram account through Meta's official OAuth flow. No passwords stored.</p>
            </div>

            <div class="glass-card feature-card">
                <div class="feature-step">02</div>
                <div class="feature-icon" style="color: #dc2743;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </div>
                <h3 class="feature-title">Fetch & Store</h3>
                <p class="feature-desc">We pull your profile data — followers, bio, posts count — directly from the Instagram Graph API and save it to our database.</p>
            </div>

            <div class="glass-card feature-card">
                <div class="feature-step">03</div>
                <div class="feature-icon" style="color: #bc1888;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                        <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                    </svg>
                </div>
                <h3 class="feature-title">Share Your Link</h3>
                <p class="feature-desc">Get a clean public profile URL. Anyone who visits sees your real follower count — no login required for them.</p>
            </div>
        </div>
    </section>
</div>

<script>
    // Auto-dismiss flash messages
    const alert = document.getElementById('flash-alert');
    if (alert) {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    }
</script>
@endsection

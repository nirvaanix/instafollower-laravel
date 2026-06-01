@extends('layouts.app')

@section('title', '@' . $profileUser->instagram_username . ' — InstaFollower')

@section('content')
<div class="container">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success" id="flash-alert">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <section class="profile-page">

        {{-- Profile Header Card --}}
        <div class="profile-hero-card glass-card">
            <div class="profile-hero-bg"></div>
            <div class="profile-hero-content">
                {{-- Avatar --}}
                <div class="profile-avatar-wrapper">
                    @if($profileUser->profile_picture_url)
                        <img src="{{ $profileUser->profile_picture_url }}" alt="{{ $profileUser->instagram_username }}" class="profile-page-avatar">
                    @else
                        <div class="profile-page-avatar-placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                    @endif
                    <div class="profile-avatar-ring"></div>
                </div>

                {{-- Username & Bio --}}
                <h1 class="profile-page-username">
                    <a href="https://instagram.com/{{ $profileUser->instagram_username }}" target="_blank" rel="noopener">
                        @{{ $profileUser->instagram_username }}
                    </a>
                </h1>

                @if($profileUser->bio)
                    <p class="profile-page-bio">{{ $profileUser->bio }}</p>
                @endif

                {{-- Follower Count — The Main Feature --}}
                <div class="follower-showcase">
                    <div class="follower-count-wrapper">
                        <div class="follower-count" id="follower-count">
                            {{ number_format($profileUser->followers_count) }}
                        </div>
                        <div class="follower-label">Followers</div>
                    </div>
                    <div class="follower-glow"></div>
                </div>

                {{-- Stats Row --}}
                <div class="profile-stats-row">
                    <div class="profile-stat">
                        <div class="profile-stat-value">{{ number_format($profileUser->followers_count) }}</div>
                        <div class="profile-stat-label">Followers</div>
                    </div>
                    <div class="profile-stat-divider"></div>
                    <div class="profile-stat">
                        <div class="profile-stat-value">{{ number_format($profileUser->media_count) }}</div>
                        <div class="profile-stat-label">Posts</div>
                    </div>
                    <div class="profile-stat-divider"></div>
                    <div class="profile-stat">
                        <div class="profile-stat-value">
                            @if($profileUser->connected_at)
                                {{ $profileUser->connected_at->format('M Y') }}
                            @else
                                —
                            @endif
                        </div>
                        <div class="profile-stat-label">Joined</div>
                    </div>
                </div>

                {{-- View on Instagram button --}}
                <a href="https://instagram.com/{{ $profileUser->instagram_username }}" target="_blank" rel="noopener" class="btn btn-instagram-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                    </svg>
                    View on Instagram
                </a>
            </div>
        </div>

        {{-- Data Source Badge --}}
        <div class="data-source-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            Data fetched from Instagram Graph API
            @if($profileUser->last_sync)
                &middot; Last synced {{ $profileUser->last_sync->diffForHumans() }}
            @endif
        </div>

        {{-- Owner Actions --}}
        @if($isOwner)
            <div class="owner-actions glass-card">
                <div class="owner-actions-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Your Profile Settings
                </div>
                <p class="owner-share-text">Share your profile link:</p>
                <div class="share-link-box">
                    <code id="share-url">{{ url('/' . $profileUser->instagram_username) }}</code>
                    <button class="btn-copy" onclick="copyLink()" id="btn-copy">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                        Copy
                    </button>
                </div>
                <form action="{{ route('instagram.disconnect') }}" method="POST" class="owner-disconnect">
                    @csrf
                    <button type="submit" class="btn btn-disconnect">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                        Disconnect Instagram
                    </button>
                </form>
            </div>
        @endif
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

    // Copy share link
    function copyLink() {
        const url = document.getElementById('share-url').textContent;
        navigator.clipboard.writeText(url).then(() => {
            const btn = document.getElementById('btn-copy');
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg> Copied!';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg> Copy';
                btn.classList.remove('copied');
            }, 2000);
        });
    }

    // Animate follower count on load
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('follower-count');
        if (!el) return;
        const target = parseInt(el.textContent.replace(/,/g, ''));
        if (isNaN(target) || target === 0) return;

        const duration = 1500;
        const startTime = performance.now();

        function easeOutExpo(t) {
            return t === 1 ? 1 : 1 - Math.pow(2, -10 * t);
        }

        function animate(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = easeOutExpo(progress);
            const current = Math.floor(easedProgress * target);
            el.textContent = current.toLocaleString();
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                el.textContent = target.toLocaleString();
            }
        }

        el.textContent = '0';
        requestAnimationFrame(animate);
    });
</script>
@endsection

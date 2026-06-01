@extends('layouts.app')

@section('title', 'Dashboard — InstaFollower')

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

    <section class="dashboard">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Your <span class="text-gradient">Accounts</span></h1>
                <p class="dashboard-subtitle">Manage your connected Instagram accounts</p>
            </div>
            <a href="{{ route('instagram.redirect') }}" class="btn btn-primary btn-add-account">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Add Account
            </a>
        </div>

        @if($accounts->count() > 0)
            {{-- Total followers across all accounts --}}
            <div class="dashboard-total glass-card">
                <div class="dashboard-total-label">Total Followers Across All Accounts</div>
                <div class="dashboard-total-value text-gradient">{{ number_format($accounts->sum('followers_count')) }}</div>
                <div class="dashboard-total-meta">{{ $accounts->count() }} account{{ $accounts->count() > 1 ? 's' : '' }} connected</div>
            </div>

            {{-- Accounts Grid --}}
            <div class="accounts-grid">
                @foreach($accounts as $account)
                    <div class="account-card glass-card {{ !$account->is_active ? 'account-inactive' : '' }}">
                        <div class="account-card-header">
                            <div class="account-card-profile">
                                @if($account->profile_picture_url)
                                    <img src="{{ $account->profile_picture_url }}" alt="{{ $account->username }}" class="account-avatar">
                                @else
                                    <div class="account-avatar-placeholder">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                    </div>
                                @endif
                                <div>
                                    <a href="/{{ $account->username }}" class="account-username">@{{ $account->username }}</a>
                                    @if($account->is_active)
                                        <span class="account-status-dot active"></span>
                                    @else
                                        <span class="account-status-dot inactive"></span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="account-card-stats">
                            <div class="account-stat">
                                <div class="account-stat-value text-gradient">{{ number_format($account->followers_count) }}</div>
                                <div class="account-stat-label">Followers</div>
                            </div>
                            <div class="account-stat">
                                <div class="account-stat-value">{{ number_format($account->media_count) }}</div>
                                <div class="account-stat-label">Posts</div>
                            </div>
                        </div>

                        <div class="account-card-footer">
                            <div class="account-share">
                                <code class="account-link">{{ url('/' . $account->username) }}</code>
                                <button class="btn-copy-sm" onclick="copyText('{{ url('/' . $account->username) }}', this)">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                                </button>
                            </div>

                            <div class="account-actions">
                                <a href="/{{ $account->username }}" class="btn-view-profile">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    View
                                </a>
                                <form action="{{ route('instagram.disconnect', $account) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-disconnect-sm" onclick="return confirm('Disconnect @{{ $account->username }}?')">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        @if($account->last_sync)
                            <div class="account-sync-time">Synced {{ $account->last_sync->diffForHumans() }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="empty-state glass-card">
                <div class="empty-icon">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </div>
                <h3>No accounts connected</h3>
                <p>Connect your first Instagram account to get started.</p>
                <a href="{{ route('instagram.redirect') }}" class="btn btn-primary" style="margin-top: 1.5rem;">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    Continue with Instagram
                </a>
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

    function copyText(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>';
                btn.classList.remove('copied');
            }, 2000);
        });
    }
</script>
@endsection

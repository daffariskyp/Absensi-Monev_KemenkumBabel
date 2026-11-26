<nav class="app-header" aria-label="Bar atas">
    <div class="app-header-inner">
        <div class="app-header-start">
            <button type="button" class="header-icon sidebar-toggle" data-sidebar-toggle
                aria-controls="app-sidebar" aria-expanded="true" aria-label="Tutup menu navigasi" hidden>
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
            <div class="app-brand">
                <img src="{{ asset('kemenkum.png') }}" alt="" width="34" height="34">
                <div class="app-brand-text">
                    <strong>{{ config('app.name') }}</strong>
                    <span>Kanwil Kemenkum Babel</span>
                </div>
            </div>
        </div>

        @php
            $user = auth()->user();
            $canReviewLeaves = in_array($user->role, ['admin', 'supervisor'], true);
            $profilePhoto = $user->employee?->photo
                ? asset('storage/' . $user->employee->photo)
                : asset('blank_profile.png');
        @endphp

        <div class="app-account">
            <div class="app-account-name">
                <strong>{{ $user->name }}</strong>
                <span>{{ $user->employee?->role?->name ?? 'Divisi belum ditetapkan' }}</span>
            </div>

            <details class="app-notifications" data-notifications
                @if($canReviewLeaves) data-url="{{ route('administration.pendingCount') }}" @endif>
                <summary class="header-icon" aria-label="Notifikasi">
                    <i class="fa-regular fa-bell" aria-hidden="true"></i>
                    <span class="notice-count" data-notice-count aria-hidden="true" hidden></span>
                </summary>
                <div class="notice-panel">
                    <h2>Notifikasi</h2>
                    <p data-notice-message role="status">{{ $canReviewLeaves ? 'Memuat notifikasi…' : 'Belum ada notifikasi.' }}</p>
                    @if($canReviewLeaves)
                        <a class="app-action" data-notice-link href="{{ route('administration.index', ['status' => 'pending']) }}" hidden>
                            Tinjau pengajuan
                        </a>
                    @endif
                </div>
            </details>

            <a class="header-avatar" href="{{ route('users.profile') }}" aria-label="Edit profil">
                <img src="{{ $profilePhoto }}" alt="" width="40" height="40">
            </a>

            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <button type="submit" class="header-icon" aria-label="Keluar" title="Keluar">
                    <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
</nav>

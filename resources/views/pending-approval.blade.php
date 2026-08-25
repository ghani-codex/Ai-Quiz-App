<x-guest-layout>
    @section('title', 'Pending approval')

    <div style="text-align: center; padding: 1rem 0;">
        <div style="display: inline-flex; width: 4rem; height: 4rem; border-radius: 9999px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
            <svg style="width: 2rem; height: 2rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
        </div>

        <h1 class="auth-title">Awaiting approval</h1>
        <p class="auth-subtitle" style="margin-bottom: 2rem;">
            Your teacher account is pending review by an admin.<br>
            You'll be notified once approved and gain access to the Teacher dashboard.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="auth-btn-primary" style="max-width: 14rem; margin: 0 auto;">Sign out</button>
        </form>
    </div>
</x-guest-layout>

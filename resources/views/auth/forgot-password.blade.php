<x-guest-layout>
    @section('title', 'Forgot password')

    <h1 class="auth-title">Forgot your password?</h1>
    <p class="auth-subtitle">
        Enter your email and we'll send you a link to reset it.
    </p>

    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-field">
            <label for="email" class="auth-label">Email address</label>
            <input id="email"
                   name="email"
                   type="email"
                   class="auth-input"
                   value="{{ old('email') }}"
                   placeholder="you@example.com"
                   required
                   autofocus>
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="auth-btn-primary" style="margin-top: 0.5rem;">Email password reset link</button>
    </form>

    <p class="auth-foot">
        Remember your password?
        <a href="{{ route('login') }}" class="auth-link">Sign in →</a>
    </p>
</x-guest-layout>

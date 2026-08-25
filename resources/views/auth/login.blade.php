<x-guest-layout>
    @section('title', 'Sign in')

    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-subtitle">Sign in to continue learning with AI Quiz.</p>

    @if (session('status'))
        <div class="auth-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
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
                   autofocus
                   autocomplete="username">
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="auth-label">Password</label>
            <input id="password"
                   name="password"
                   type="password"
                   class="auth-input"
                   placeholder="••••••••"
                   required
                   autocomplete="current-password">
            @error('password') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-row">
            <label class="auth-check">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="auth-btn-primary">Sign in</button>
    </form>

    <p class="auth-foot">
        Don't have an account?
        <a href="{{ route('register') }}" class="auth-link">Create one →</a>
    </p>
</x-guest-layout>

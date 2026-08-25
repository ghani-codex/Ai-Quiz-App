<x-guest-layout>
    @section('title', 'Reset password')

    <h1 class="auth-title">Set a new password</h1>
    <p class="auth-subtitle">Choose a strong password you don't reuse elsewhere.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-field">
            <label for="email" class="auth-label">Email address</label>
            <input id="email"
                   name="email"
                   type="email"
                   class="auth-input"
                   value="{{ old('email', $request->email) }}"
                   required
                   autofocus
                   autocomplete="username">
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="auth-label">New password</label>
            <input id="password"
                   name="password"
                   type="password"
                   class="auth-input"
                   placeholder="At least 8 characters"
                   required
                   autocomplete="new-password">
            @error('password') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field" style="margin-bottom: 1.5rem;">
            <label for="password_confirmation" class="auth-label">Confirm new password</label>
            <input id="password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="auth-input"
                   placeholder="Repeat your password"
                   required
                   autocomplete="new-password">
        </div>

        <button type="submit" class="auth-btn-primary">Reset password</button>
    </form>
</x-guest-layout>

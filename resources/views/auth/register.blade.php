<x-guest-layout>
    @section('title', 'Create account')

    <h1 class="auth-title">Create your account</h1>
    <p class="auth-subtitle">Pick your role and start in under a minute.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label class="auth-label" style="margin-bottom: 0.5rem;">I want to register as</label>
        <div class="role-grid">
            <label class="role-card">
                <input type="radio" name="role" value="student" {{ old('role', 'student') === 'student' ? 'checked' : '' }}>
                <span class="role-card-tick">
                    <svg style="width:0.75rem; height:0.75rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div class="role-card-head">
                    <span class="role-card-icon">
                        <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                    </span>
                    <span class="role-card-title">Student</span>
                </div>
                <p class="role-card-desc">Enrol in courses and take quizzes</p>
            </label>

            <label class="role-card">
                <input type="radio" name="role" value="teacher" {{ old('role') === 'teacher' ? 'checked' : '' }}>
                <span class="role-card-tick">
                    <svg style="width:0.75rem; height:0.75rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </span>
                <div class="role-card-head">
                    <span class="role-card-icon">
                        <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                    </span>
                    <span class="role-card-title">Teacher</span>
                </div>
                <p class="role-card-desc">Create courses · needs admin approval</p>
            </label>
        </div>
        @error('role') <p class="auth-error" style="margin-top:-0.75rem; margin-bottom: 1rem;">{{ $message }}</p> @enderror

        <div class="auth-field">
            <label for="name" class="auth-label">Full name</label>
            <input id="name"
                   name="name"
                   type="text"
                   class="auth-input"
                   value="{{ old('name') }}"
                   placeholder="Haseeb Ur Rehman"
                   required
                   autofocus
                   autocomplete="name">
            @error('name') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="email" class="auth-label">Email address</label>
            <input id="email"
                   name="email"
                   type="email"
                   class="auth-input"
                   value="{{ old('email') }}"
                   placeholder="you@example.com"
                   required
                   autocomplete="username">
            @error('email') <p class="auth-error">{{ $message }}</p> @enderror
        </div>

        <div class="auth-field">
            <label for="password" class="auth-label">Password</label>
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
            <label for="password_confirmation" class="auth-label">Confirm password</label>
            <input id="password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="auth-input"
                   placeholder="Repeat your password"
                   required
                   autocomplete="new-password">
        </div>

        <button type="submit" class="auth-btn-primary">Create account</button>
    </form>

    <p class="auth-foot">
        Already have an account?
        <a href="{{ route('login') }}" class="auth-link">Sign in →</a>
    </p>
</x-guest-layout>

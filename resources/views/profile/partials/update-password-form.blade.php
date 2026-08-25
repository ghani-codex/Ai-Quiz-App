<section class="acc-card">
    <div class="acc-card-head">
        <h2 class="acc-card-title">Change password</h2>
        <p class="acc-card-sub">Use a long, unique password to keep your account secure.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="auth-field">
            <label for="update_password_current_password" class="auth-label">Current password</label>
            <input id="update_password_current_password"
                   name="current_password"
                   type="password"
                   class="auth-input"
                   placeholder="Your current password"
                   autocomplete="current-password">
            @if ($errors->updatePassword->get('current_password'))
                <p class="auth-error">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div class="auth-row-2">
            <div class="auth-field">
                <label for="update_password_password" class="auth-label">New password</label>
                <input id="update_password_password"
                       name="password"
                       type="password"
                       class="auth-input"
                       placeholder="At least 8 characters"
                       autocomplete="new-password">
                @if ($errors->updatePassword->get('password'))
                    <p class="auth-error">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div class="auth-field">
                <label for="update_password_password_confirmation" class="auth-label">Confirm new password</label>
                <input id="update_password_password_confirmation"
                       name="password_confirmation"
                       type="password"
                       class="auth-input"
                       placeholder="Repeat new password"
                       autocomplete="new-password">
                @if ($errors->updatePassword->get('password_confirmation'))
                    <p class="auth-error">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>
        </div>

        <div style="display:flex; align-items:center; margin-top: 0.5rem;">
            <button type="submit" class="auth-btn-primary">
                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                Update password
            </button>

            @if (session('status') === 'password-updated')
                <span class="acc-saved"
                      x-data="{ show: true }"
                      x-show="show"
                      x-transition
                      x-init="setTimeout(() => show = false, 3000)">
                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Password updated
                </span>
            @endif
        </div>
    </form>

</section>

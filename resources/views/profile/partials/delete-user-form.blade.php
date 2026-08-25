<section class="acc-card acc-card-danger"
         x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
         @keydown.escape.window="open = false">
    <div class="acc-card-head">
        <h2 class="acc-card-title">Delete account</h2>
        <p class="acc-card-sub">Permanently remove your account and all associated data.</p>
    </div>

    <div class="danger-warn">
        <svg class="danger-warn-icon" style="width:1.125rem; height:1.125rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
        <div>
            This action is <strong style="color:#fca5a5;">permanent and irreversible</strong>.
            All your courses, enrollments, lecture views, quiz attempts and uploaded files will be erased forever.
        </div>
    </div>

    <button type="button"
            class="auth-btn-danger"
            @click="open = true">
        <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
        </svg>
        Delete my account
    </button>

    {{-- Confirmation modal --}}
    <div x-show="open"
         x-cloak
         x-transition.opacity.duration.150ms
         class="acc-modal-backdrop"
         @click.self="open = false">
        <div class="acc-modal-panel" @click.stop>
            <div class="acc-modal-icon">
                <svg style="width:1.5rem; height:1.5rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
            </div>

            <h2 class="acc-modal-title">Delete your account?</h2>
            <p class="acc-modal-desc">
                This is permanent and can't be undone. Type your password below to confirm.
            </p>

            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="auth-field" style="margin-bottom: 0;">
                    <label for="delete_password" class="auth-label">Password</label>
                    <input id="delete_password"
                           name="password"
                           type="password"
                           class="auth-input"
                           placeholder="Your current password"
                           autocomplete="current-password"
                           autofocus>
                    @if ($errors->userDeletion->get('password'))
                        <p class="auth-error">{{ $errors->userDeletion->first('password') }}</p>
                    @endif
                </div>

                <div class="acc-modal-actions">
                    <button type="button"
                            class="auth-btn-secondary"
                            @click="open = false">
                        Cancel
                    </button>
                    <button type="submit" class="auth-btn-danger">
                        Yes, delete my account
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

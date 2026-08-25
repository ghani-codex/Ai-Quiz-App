@php
    $user = $user ?? auth()->user();
    $avatarUrl = $user->getFilamentAvatarUrl();
@endphp

<section class="acc-card">
    <div class="acc-card-head">
        <h2 class="acc-card-title">Profile information</h2>
        <p class="acc-card-sub">Your name, photo, and contact details — visible across your dashboard.</p>
    </div>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Avatar row --}}
        <div class="acc-avatar-row"
             x-data="{
                preview: '{{ $avatarUrl }}',
                filename: '',
                pick(event) {
                    const f = event.target.files[0];
                    if (!f) return;
                    this.filename = f.name;
                    const reader = new FileReader();
                    reader.onload = e => this.preview = e.target.result;
                    reader.readAsDataURL(f);
                }
             }">
            <div class="acc-avatar">
                <img :src="preview" alt="Avatar preview">
            </div>
            <div class="acc-avatar-actions">
                <p class="acc-avatar-title">Profile photo</p>
                <p class="acc-avatar-desc">JPG, PNG or WEBP · Max 2 MB · A square image works best.</p>
                <label class="acc-file-btn">
                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 7.5m0 0L7.5 12M12 7.5V21"/></svg>
                    Upload new photo
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" class="acc-file-input" @change="pick($event)">
                </label>
                <span class="acc-file-name" x-text="filename"></span>
                @error('avatar')<p class="auth-error" style="margin-top:0.5rem;">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Two-column row: name + email --}}
        <div class="auth-row-2">
            <div class="auth-field">
                <label for="name" class="auth-label">Full name</label>
                <input id="name"
                       name="name"
                       type="text"
                       class="auth-input"
                       value="{{ old('name', $user->name) }}"
                       required
                       autocomplete="name">
                @error('name')<p class="auth-error">{{ $message }}</p>@enderror
            </div>

            <div class="auth-field">
                <label for="email" class="auth-label">Email address</label>
                <input id="email"
                       name="email"
                       type="email"
                       class="auth-input"
                       value="{{ old('email', $user->email) }}"
                       required
                       autocomplete="email">
                @error('email')<p class="auth-error">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Country + Bio --}}
        <div class="auth-field">
            <label for="country" class="auth-label">Country</label>
            <input id="country"
                   name="country"
                   type="text"
                   class="auth-input"
                   value="{{ old('country', $user->country) }}"
                   placeholder="Pakistan"
                   maxlength="80">
            @error('country')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div class="auth-field" style="margin-bottom: 1.5rem;">
            <label for="bio" class="auth-label">Bio</label>
            <textarea id="bio"
                      name="bio"
                      class="auth-textarea"
                      rows="3"
                      placeholder="Tell others a bit about yourself…"
                      maxlength="2000">{{ old('bio', $user->bio) }}</textarea>
            <p class="auth-hint">Shown on your public instructor / student profile.</p>
            @error('bio')<p class="auth-error">{{ $message }}</p>@enderror
        </div>

        <div style="display:flex; align-items:center;">
            <button type="submit" class="auth-btn-primary">
                <svg style="width:1rem; height:1rem;" fill="none" stroke="currentColor" stroke-width="2.25" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75 10.5 18.75 19.5 5.25"/></svg>
                Save changes
            </button>

            @if (session('status') === 'profile-updated')
                <span class="acc-saved"
                      x-data="{ show: true }"
                      x-show="show"
                      x-transition
                      x-init="setTimeout(() => show = false, 3000)">
                    <svg style="width:0.875rem; height:0.875rem;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Profile saved
                </span>
            @endif
        </div>
    </form>
</section>

{{-- Change password and delete account: same routes as before, opened in place. --}}
<section class="lv-flow-card" aria-labelledby="security-title">
    <h2 id="security-title" class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.profile.security') }}</h2>

    <details class="lv-account-details" @if($errors->updatePassword->isNotEmpty()) open @endif>
        <summary class="lv-t-label-m lv-tone-terracotta">{{ __('account.profile.change_password') }}</summary>
        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-4 pt-4">
            @csrf
            @method('put')
            <x-site.molecules.form-field name="current_password" type="password" :label="__('account.fields.current_password')" error-bag="updatePassword" autocomplete="current-password" required />
            <div class="lv-form-grid">
                <x-site.molecules.form-field name="password" type="password" :label="__('ui.fields.password')" error-bag="updatePassword" autocomplete="new-password" required />
                <x-site.molecules.form-field name="password_confirmation" type="password" :label="__('account.fields.password_confirm')" error-bag="updatePassword" autocomplete="new-password" required />
            </div>
            <x-site.atoms.button type="submit" variant="outline" :arrow="false" class="self-start">{{ __('account.profile.change_password') }}</x-site.atoms.button>
        </form>
    </details>

    <details class="lv-account-details" @if($errors->userDeletion->isNotEmpty()) open @endif>
        <summary class="lv-t-label-s lv-tone-terracotta">{{ __('account.profile.delete') }}</summary>
        <form method="POST" action="{{ route('profile.destroy') }}" class="flex flex-col gap-4 pt-4">
            @csrf
            @method('delete')
            <p class="lv-t-body-s lv-tone-muted">{{ __('account.profile.delete_help') }}</p>
            <x-site.molecules.form-field name="password" id="delete_password" type="password" :label="__('ui.fields.password')" error-bag="userDeletion" autocomplete="current-password" required />
            <x-site.atoms.button type="submit" variant="outline" :arrow="false" class="self-start">{{ __('account.profile.delete') }}</x-site.atoms.button>
        </form>
    </details>
</section>

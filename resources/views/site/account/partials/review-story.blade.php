{{-- Written review with prompts + photo picker (previews only; files post with the form). --}}
<section class="lv-flow-card" x-data="{ body: @js(old('body', '')), add(prompt) { this.body = (this.body ? this.body + '\n' : '') + prompt + ' '; this.$refs.body.focus(); } }">
    <div>
        <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.review.tell') }}</h2>
        <p class="lv-t-body-s lv-tone-muted">{{ __('account.review.tell_help') }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @foreach(['moment', 'surprise', 'tip'] as $prompt)
            <button type="button" class="lv-account-chip lv-t-label-s" @click="add(@js(__('account.review.prompts.'.$prompt)))">+ {{ __('account.review.prompts.'.$prompt) }}</button>
        @endforeach
    </div>
    <div class="lv-form-field">
        <label for="body" class="sr-only">{{ __('account.review.tell') }}</label>
        <textarea id="body" name="body" x-ref="body" x-model="body" rows="4" minlength="10" maxlength="2000" required class="lv-form-input lv-form-input--area"></textarea>
        <div class="flex justify-between gap-3 lv-t-caption lv-tone-muted">
            <span>{{ __('account.review.shown_as') }}</span>
            <span><span x-text="body.length">0</span> / 2000</span>
        </div>
        @error('body')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
    </div>
</section>

<section class="lv-flow-card" x-data="{ previews: [], pick(event) { this.previews = [...event.target.files].slice(0, 6).map((file) => URL.createObjectURL(file)); } }">
    <div>
        <h2 class="lv-t-h4 lv-serif lv-tone-ink">{{ __('account.review.photos') }}</h2>
        <p class="lv-t-body-s lv-tone-muted">{{ __('account.review.photos_help') }}</p>
    </div>
    <div class="flex flex-wrap gap-3">
        <template x-for="preview in previews" :key="preview">
            <img :src="preview" alt="" width="108" height="108" class="lv-account-photo">
        </template>
        <label class="lv-account-photo lv-account-photo--add lv-t-label-s lv-tone-terracotta">
            <span aria-hidden="true">+</span>{{ __('account.review.add_photo') }}
            <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="sr-only" @change="pick($event)">
        </label>
    </div>
    @error('photos')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
    @error('photos.*')<p class="lv-form-error lv-t-caption" role="alert">{{ $message }}</p>@enderror
</section>

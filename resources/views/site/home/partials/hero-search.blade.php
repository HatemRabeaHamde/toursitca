<div class="hero-search" id="experiences">
    <div class="hs-box">
        <form method="GET" action="{{ route('site.experiences.index', ['locale' => app()->getLocale()]) }}" class="contents">
            <div class="hs-f">
                <label for="home-city">{{ __('ui.landing.search.destination') }}</label>
                <input id="home-city" name="city" type="text" placeholder="{{ __('ui.landing.city_placeholder') }}">
            </div>

            <div class="hs-f">
                <label for="home-category">{{ __('ui.landing.search.category') }}</label>
                <select id="home-category" name="category">
                    <option value="">{{ __('ui.landing.all_experiences') }}</option>
                    @foreach (__('ui.categories') as $slug => $label)
                        <option value="{{ $slug }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="hs-f">
                <label for="home-date">{{ __('ui.landing.search.date') }}</label>
                <input id="home-date" name="date" type="date">
            </div>

            <div class="hs-f" style="border-right:none;">
                <label for="home-travelers">{{ __('ui.landing.search.travelers') }}</label>
                <input id="home-travelers" name="participants" type="number" min="1" max="{{ config('booking.max_participants') }}" placeholder="{{ __('ui.landing.search.travelers_placeholder') }}">
            </div>

            <div class="hs-go">
                <button class="hs-go-btn" type="submit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    {{ __('ui.landing.search.submit') }}
                </button>
            </div>
        </form>
    </div>
</div>


@if ($landing->categories->isNotEmpty())
    <section class="cats-section">
        <div class="cats-inner">
            <p class="sec-label" style="justify-content:center;">{{ __('ui.landing.sections.categories_label') }}</p>
            <h2 class="sec-h2">{{ __('ui.landing.sections.categories_title') }}</h2>
        </div>

        <div class="cats-row">
            @foreach ($landing->categories as $category)
                <a class="cat-item" href="{{ $category['url'] }}">
                    <div class="cat-circle">
                        <img src="{{ $category['image_url'] }}" alt="{{ $category['name'] }}" loading="lazy">
                    </div>
                    <p class="cat-name">{{ $category['name'] }}</p>
                </a>
            @endforeach
        </div>
    </section>
@endif

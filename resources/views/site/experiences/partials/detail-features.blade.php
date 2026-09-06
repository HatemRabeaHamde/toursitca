@if ($features)
    <div class="ed-feature-grid">
        @foreach ($features as $feature)
            <div class="ed-feature">
                <p class="ed-feature-label">{{ $feature['label'] }}</p>
                <p class="ed-feature-value">{{ $feature['value'] }}</p>
            </div>
        @endforeach
    </div>
@endif

@props([
    'action',
    'agencies'   => collect(),
    'categories' => collect(),
    'experience' => null,
    'method'     => 'POST',
    'showAgencySelect' => false,
    'submitLabel' => __('ui.nav.create_experience'),
])


<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="xp-form-stack" x-data="experienceFormGuard()" x-on:submit="validate($event)" novalidate>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    @php
        $locationLat = old('location_lat', $experience?->location_lat);
        $locationLng = old('location_lng', $experience?->location_lng);

        $inclusionsText = old('inclusions_text');
        if ($inclusionsText === null && $experience?->inclusions) {
            $inclusionsText = implode(PHP_EOL, (array) $experience->getTranslation('inclusions', 'en', false));
        }

        $exclusionsText = old('exclusions_text');
        if ($exclusionsText === null && $experience?->exclusions) {
            $exclusionsText = implode(PHP_EOL, (array) $experience->getTranslation('exclusions', 'en', false));
        }

        $itineraryRows = old('itinerary');
        if ($itineraryRows === null) {
            $itineraryRows = $experience?->itineraryItems?->map(fn ($item) => [
                'sort_order' => $item->sort_order,
                'type' => $item->type,
                'title' => $item->getTranslations('title'),
                'description' => $item->getTranslations('description'),
                'duration_minutes' => $item->duration_minutes,
                'location_name' => $item->getTranslations('location_name'),
                'location_lat' => $item->location_lat,
                'location_lng' => $item->location_lng,
                'is_main_stop' => $item->is_main_stop,
                'icon' => $item->icon,
            ])->values()->all() ?? [];
        }

        $existingImages = $experience?->media?->where('type', 'image')->values() ?? collect();
        $existingVideos = $experience?->media?->where('type', 'video')->values() ?? collect();
        $savedVideoUrl = old('video_url', $existingVideos->firstWhere('source_type', 'url')?->path);
    @endphp

    <div class="xp-form-client-alert" x-show="message" x-cloak x-transition role="alert">
        <strong>Check required fields</strong>
        <span x-text="message"></span>
    </div>

    @if ($errors->any())
        <x-ui.alert type="error" class="xp-form-alert">
            <strong>Some fields need attention.</strong>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-ui.alert>
    @endif

    {{-- ── Agency (admin only) ────────────────────────────────── --}}
    @if ($showAgencySelect)
        <x-ui.card title="Ownership">
            <x-ui.select
                name="agency_id"
                :label="__('ui.fields.agency_name')"
                :options="$agencies->mapWithKeys(fn($a) => [$a->id => $a->name . ($a->is_platform ? ' — Platform' : '')])->toArray()"
                :selected="old('agency_id', $experience?->agency_id)"
                :placeholder="__('ui.fields.platform_agency_default')"
            />
        </x-ui.card>
    @endif

    {{-- ── Title ───────────────────────────────────────────────── --}}
    <x-ui.card :title="__('ui.fields.title')">
        <div class="xp-form-primary-field">
            <x-ui.input
                name="title[en]"
                :label="__('ui.fields.title') . ' — English'"
                :value="old('title.en', $experience?->getTranslation('title', 'en', false))"
                required
                placeholder="e.g. Sunset Camel Trek in the Sahara"
            />
        </div>

        <div class="xp-form-secondary-grid">
            <div class="xp-form-optional-badge">{{ __('ui.form.optional_translations') }}</div>
            <div class="xp-form-optional-fields">
                <x-ui.input
                    name="title[fr]"
                    :label="__('ui.fields.title') . ' — Français'"
                    :value="old('title.fr', $experience?->getTranslation('title', 'fr', false))"
                    placeholder="Optionnel"
                />
                <x-ui.input
                    name="title[nl]"
                    :label="__('ui.fields.title') . ' — Nederlands'"
                    :value="old('title.nl', $experience?->getTranslation('title', 'nl', false))"
                    placeholder="Optioneel"
                />
            </div>
        </div>
    </x-ui.card>

    {{-- ── Description ─────────────────────────────────────────── --}}
    <x-ui.card :title="__('ui.fields.description')">
        <div class="xp-form-primary-field">
            <x-ui.textarea
                name="description[en]"
                :label="__('ui.fields.description') . ' — English'"
                :value="old('description.en', $experience?->getTranslation('description', 'en', false))"
                :rows="6"
                required
                placeholder="Describe the experience in detail — what guests will see, do, and feel."
            />
        </div>

        <div class="xp-form-secondary-grid">
            <div class="xp-form-optional-badge">{{ __('ui.form.optional_translations') }}</div>
            <div class="xp-form-optional-fields">
                <x-ui.textarea
                    name="description[fr]"
                    :label="__('ui.fields.description') . ' — Français'"
                    :value="old('description.fr', $experience?->getTranslation('description', 'fr', false))"
                    :rows="4"
                    placeholder="Optionnel"
                />
                <x-ui.textarea
                    name="description[nl]"
                    :label="__('ui.fields.description') . ' — Nederlands'"
                    :value="old('description.nl', $experience?->getTranslation('description', 'nl', false))"
                    :rows="4"
                    placeholder="Optioneel"
                />
            </div>
        </div>
    </x-ui.card>

    {{-- ── Basics ──────────────────────────────────────────────── --}}
    <x-ui.card title="Basics">
        <div class="xp-form-grid-3">
            {{-- Category — dropdown from DB --}}
            <x-ui.select
                name="category"
                :label="__('ui.fields.category')"
                :options="$categories->mapWithKeys(fn($c) => [$c->slug => $c->getTranslation('name', 'en')])->toArray()"
                :selected="old('category', $experience?->category)"
                :placeholder="'— ' . __('ui.fields.category') . ' —'"
                required
            />
            <x-ui.select name="difficulty" :label="__('ui.fields.difficulty')" :options="[
                'easy'     => __('ui.difficulty.easy'),
                'moderate' => __('ui.difficulty.moderate'),
                'hard'     => __('ui.difficulty.hard'),
            ]" :selected="old('difficulty', $experience?->difficulty ?? 'easy')" required />
            <x-ui.input name="duration_hours" type="number" step="0.5" min="0.5" :label="__('ui.fields.duration_hours')" :value="old('duration_hours', $experience?->duration_hours)" required placeholder="3" />
            <x-ui.input name="max_group_size" type="number" min="1" :label="__('ui.fields.max_group_size')" :value="old('max_group_size', $experience?->max_group_size)" required placeholder="12" />
            <x-ui.input name="price_per_person" type="number" step="0.01" min="0" :label="__('ui.fields.price_per_person') . ' (MAD)'" :value="old('price_per_person', $experience?->price_per_person)" required placeholder="650" />
            <x-ui.input name="original_price" type="number" step="0.01" min="0" :label="__('ui.fields.original_price') . ' (MAD)'" :value="old('original_price', $experience?->original_price)" placeholder="Optional — for crossed-out price" />
        </div>
        <div class="xp-form-grid-3 mt-5">
            <x-ui.input name="private_price" type="number" step="0.01" min="0" :label="__('ui.fields.private_price') . ' (MAD)'" :value="old('private_price', $experience?->private_price)" placeholder="Optional" />
            <x-ui.input name="deal_starts_at" type="datetime-local" :label="__('ui.fields.deal_starts_at')" :value="old('deal_starts_at', $experience?->deal_starts_at?->format('Y-m-d\\TH:i'))" />
            <x-ui.input name="deal_ends_at" type="datetime-local" :label="__('ui.fields.deal_ends_at')" :value="old('deal_ends_at', $experience?->deal_ends_at?->format('Y-m-d\\TH:i'))" />
        </div>
        <div class="xp-form-checkboxes mt-5">
            <label class="xp-form-check">
                <input type="checkbox" name="pickup_enabled" value="1" @checked(old('pickup_enabled', $experience?->pickup_enabled ?? false))>
                <span>{{ __('ui.fields.pickup_enabled') }}</span>
            </label>
            <label class="xp-form-check">
                <input type="checkbox" name="is_top_rated" value="1" @checked(old('is_top_rated', $experience?->is_top_rated ?? false))>
                <span>{{ __('ui.fields.is_top_rated') }}</span>
            </label>
        </div>
    </x-ui.card>

    {{-- ── Itinerary ───────────────────────────────────────────── --}}
    <x-ui.card title="Itinerary">
        <div
            class="xp-itinerary-builder"
            x-data="itineraryBuilder(@js($itineraryRows))"
        >
            <div class="xp-itinerary-builder-head">
                <div>
                    <p class="xp-itinerary-builder-kicker">Route story</p>
                    <p class="xp-itinerary-builder-help">Add real stops only. These appear on the public detail page with a timeline and map.</p>
                </div>
                <button type="button" class="xp-inline-add" @click="add()">+ Add stop</button>
            </div>

            <template x-for="(item, index) in items" :key="item.key">
                <div class="xp-itinerary-row">
                    <div class="xp-itinerary-row-head">
                        <strong x-text="'Stop ' + (index + 1)"></strong>
                        <button type="button" class="xp-inline-remove" @click="remove(index)">Remove</button>
                    </div>

                    <input type="hidden" :name="`itinerary[${index}][sort_order]`" :value="index + 1">

                    <div class="xp-form-grid-3">
                        <label class="field">
                            <span class="label">Type</span>
                            <select class="input" :name="`itinerary[${index}][type]`" x-model="item.type">
                                <option value="start">Starting location</option>
                                <option value="transport">Transport</option>
                                <option value="stop">Stop</option>
                                <option value="activity">Activity</option>
                                <option value="dropoff">Drop-off</option>
                            </select>
                        </label>
                        <label class="field">
                            <span class="label">Icon</span>
                            <select class="input" :name="`itinerary[${index}][icon]`" x-model="item.icon">
                                <option value="pin">Pin</option>
                                <option value="walk">Walk</option>
                                <option value="car">Car</option>
                                <option value="bus">Bus</option>
                                <option value="boat">Boat</option>
                                <option value="food">Food</option>
                                <option value="photo">Photo</option>
                                <option value="camp">Camp</option>
                                <option value="finish">Finish</option>
                            </select>
                        </label>
                        <label class="field">
                            <span class="label">Duration minutes</span>
                            <input class="input" type="number" min="1" max="10080" :name="`itinerary[${index}][duration_minutes]`" x-model="item.duration_minutes" placeholder="30">
                        </label>
                    </div>

                    <div class="xp-form-grid-3 mt-4">
                        <label class="field">
                            <span class="label">Title — English</span>
                            <input class="input" type="text" :name="`itinerary[${index}][title][en]`" x-model="item.title.en" placeholder="Ferry ride">
                        </label>
                        <label class="field">
                            <span class="label">Title — Français</span>
                            <input class="input" type="text" :name="`itinerary[${index}][title][fr]`" x-model="item.title.fr" placeholder="Optionnel">
                        </label>
                        <label class="field">
                            <span class="label">Title — Nederlands</span>
                            <input class="input" type="text" :name="`itinerary[${index}][title][nl]`" x-model="item.title.nl" placeholder="Optioneel">
                        </label>
                    </div>

                    <div class="xp-form-grid-2 mt-4">
                        <label class="field">
                            <span class="label">Location name — English</span>
                            <input class="input" type="text" :name="`itinerary[${index}][location_name][en]`" x-model="item.location_name.en" placeholder="Jemaa el-Fnaa">
                        </label>
                        <label class="field">
                            <span class="label">Description — English</span>
                            <input class="input" type="text" :name="`itinerary[${index}][description][en]`" x-model="item.description.en" placeholder="Visit, free time, guided walk...">
                        </label>
                    </div>

                    <div class="xp-form-grid-3 mt-4">
                        <label class="field">
                            <span class="label">Latitude</span>
                            <input class="input" type="number" step="0.0000001" :name="`itinerary[${index}][location_lat]`" x-model="item.location_lat" placeholder="31.6295">
                        </label>
                        <label class="field">
                            <span class="label">Longitude</span>
                            <input class="input" type="number" step="0.0000001" :name="`itinerary[${index}][location_lng]`" x-model="item.location_lng" placeholder="-7.9811">
                        </label>
                        <label class="xp-form-check xp-itinerary-main-check">
                            <input type="checkbox" :name="`itinerary[${index}][is_main_stop]`" value="1" x-model="item.is_main_stop">
                            <span>Main stop</span>
                        </label>
                    </div>

                    <div class="xp-itinerary-map-picker mt-4" x-data="itineraryMapPicker(item)">
                        <button type="button" class="xp-map-toggle" @click="toggle($refs.map)">
                            <span x-text="open ? 'Hide stop map' : 'Pick stop on map'"></span>
                        </button>
                        <div class="xp-itinerary-map-shell" x-show="open" x-transition x-cloak>
                            <p class="xp-map-hint">Click on the map to fill this stop latitude and longitude.</p>
                            <div x-ref="map" class="xp-map xp-map--compact"></div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="xp-itinerary-empty" x-show="items.length === 0">
                <p>No itinerary yet. Add the real route stops when you have them.</p>
            </div>
        </div>
    </x-ui.card>

    {{-- ── Location ─────────────────────────────────────────────── --}}
    <x-ui.card title="Location">
        <div class="xp-form-grid-3 mb-5">
            <x-ui.input name="location_city" :label="__('ui.fields.location_city')" :value="old('location_city', $experience?->location_city)" required placeholder="Marrakesh" />
            <x-ui.input
                name="location_lat"
                id="location_lat"
                type="number"
                step="0.0000001"
                :label="__('ui.fields.location_lat')"
                :value="old('location_lat', $experience?->location_lat)"
                placeholder="31.6295"
            />
            <x-ui.input
                name="location_lng"
                id="location_lng"
                type="number"
                step="0.0000001"
                :label="__('ui.fields.location_lng')"
                :value="old('location_lng', $experience?->location_lng)"
                placeholder="-7.9811"
            />
        </div>

        {{-- Morocco map picker --}}
        <div
            class="xp-map-wrap"
            x-data="mapPicker({
                lat: @js(filled($locationLat) ? (float) $locationLat : 31.7917),
                lng: @js(filled($locationLng) ? (float) $locationLng : -7.0926)
            })"
            x-init="init()"
        >
            <p class="xp-map-hint">Click on the map to set the exact meeting point coordinates.</p>
            <div id="xp-map" class="xp-map"></div>
        </div>

        <div class="mt-5">
            <x-ui.textarea name="meeting_point" :label="__('ui.fields.meeting_point')" :value="old('meeting_point', $experience?->meeting_point)" placeholder="Meeting point instructions for guests — be specific." />
        </div>
    </x-ui.card>

    {{-- ── Inclusions & Exclusions ──────────────────────────────── --}}
    <x-ui.card title="Inclusions &amp; Exclusions">
        <div class="xp-form-grid-2">
            <x-ui.textarea name="inclusions_text" :label="__('ui.fields.inclusions')" :value="$inclusionsText" placeholder="One item per line&#10;e.g. Hotel pickup&#10;Bottled water&#10;Licensed guide" />
            <x-ui.textarea name="exclusions_text" :label="__('ui.fields.exclusions')" :value="$exclusionsText" placeholder="One item per line&#10;e.g. Gratuities&#10;Personal expenses" />
        </div>
    </x-ui.card>

    {{-- ── Media ────────────────────────────────────────────────── --}}
    <x-ui.card title="Media">
        @if ($existingImages->isNotEmpty() || $existingVideos->isNotEmpty())
            <div class="xp-existing-media">
                @if ($existingImages->isNotEmpty())
                    <div>
                        <p class="xp-existing-media-title">Saved photos</p>
                        <div class="xp-existing-media-grid">
                            @foreach ($existingImages as $image)
                                <a href="{{ $image->publicUrl() }}" target="_blank" rel="noopener" class="xp-existing-media-thumb">
                                    <img src="{{ $image->publicUrl() }}" alt="">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($existingVideos->isNotEmpty())
                    <div>
                        <p class="xp-existing-media-title">Saved videos</p>
                        <div class="xp-existing-video-list">
                            @foreach ($existingVideos as $video)
                                <a href="{{ $video->publicUrl() }}" target="_blank" rel="noopener" class="xp-existing-video-link">
                                    <span>{{ $video->source_type === 'url' ? 'Video URL' : 'Uploaded video' }}</span>
                                    <small>{{ $video->path }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Photos --}}
        <div class="mb-6">
            <span class="label">{{ __('ui.fields.images') }}</span>
            <label class="xp-file-drop" id="photo-drop-zone">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                <span class="xp-file-drop-text">Click to choose photos</span>
                <span class="xp-file-drop-hint">JPG, PNG, WebP — max 5 MB each</span>
                <input id="photo-input" name="images[]" type="file" multiple accept="image/*" class="sr-only">
            </label>
            @error('images.*') <span class="error">{{ $message }}</span> @enderror
            <div id="photo-previews" class="xp-photo-previews"></div>
        </div>

        {{-- Video URL + preview --}}
        <div
            x-data="videoPreview()"
            x-init="init(@js($savedVideoUrl))"
        >
            <div class="mb-5">
                <span class="label">{{ __('ui.fields.video_file') }}</span>
                <label class="xp-file-drop">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                    <span class="xp-file-drop-text">Click to choose a video file</span>
                    <span class="xp-file-drop-hint">MP4, WebM, MOV — max 50 MB</span>
                    <input name="video_file" type="file" accept="video/mp4,video/webm,video/quicktime" class="sr-only">
                </label>
                @error('video_file') <span class="error">{{ $message }}</span> @enderror
            </div>

            <x-ui.input
                name="video_url"
                type="url"
                :label="__('ui.fields.video_url')"
                :value="$savedVideoUrl"
                placeholder="https://www.youtube.com/watch?v=... or direct .mp4 URL"
                x-on:input.debounce.600="preview($event.target.value)"
                x-on:paste="$nextTick(() => preview($event.target.value))"
            />
            <div x-show="embedUrl || videoUrl" x-cloak class="xp-video-preview mt-4">
                {{-- YouTube / Vimeo iframe --}}
                <template x-if="embedUrl">
                    <iframe
                        :src="embedUrl"
                        class="xp-video-iframe"
                        frameborder="0"
                        allow="autoplay; encrypted-media; picture-in-picture"
                        allowfullscreen
                    ></iframe>
                </template>
                {{-- Direct video file --}}
                <template x-if="!embedUrl && videoUrl">
                    <video :src="videoUrl" class="xp-video-iframe" controls playsinline></video>
                </template>
            </div>
        </div>
    </x-ui.card>

    {{-- ── Submit ───────────────────────────────────────────────── --}}
    <div class="xp-form-footer">
        <x-ui.button type="submit" variant="primary">{{ $submitLabel }}</x-ui.button>
    </div>

</form>

@push('scripts')
<script>
async function ensureLeafletAssets() {
    if (!document.querySelector('link[href*="leaflet"]')) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
    }

    if (typeof L !== 'undefined') {
        return;
    }

    await new Promise((resolve, reject) => {
        const existing = document.querySelector('script[src*="leaflet"]');
        if (existing) {
            existing.addEventListener('load', resolve, { once: true });
            existing.addEventListener('error', reject, { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

function experienceMapIcon() {
    return L.divIcon({
        className: '',
        html: '<div class="xp-map-pin"></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 28],
    });
}

function experienceFormGuard() {
    return {
        message: '',
        validate(event) {
            this.message = '';
            const invalid = event.target.querySelector(':invalid');

            if (!invalid) {
                return;
            }

            event.preventDefault();
            this.message = invalid.validationMessage || 'Please complete the highlighted required field before saving.';
            invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            invalid.reportValidity?.();
            setTimeout(() => invalid.focus({ preventScroll: true }), 250);
        },
    };
}

/* ── Map picker (Leaflet lazy-loaded + Alpine.js) ──────────── */
function mapPicker({ lat, lng }) {
    return {
        map: null,
        marker: null,
        async init() {
            await ensureLeafletAssets();

            const startLat = lat || 31.7917;
            const startLng = lng || -7.0926;
            const zoom = (lat && lng) ? 11 : 6;

            this.map = L.map('xp-map').setView([startLat, startLng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(this.map);

            const icon = experienceMapIcon();

            if (lat && lng) {
                this.marker = L.marker([lat, lng], { icon, draggable: true }).addTo(this.map);
                this.marker.on('dragend', e => this._setCoords(e.target.getLatLng()));
            }

            this.map.on('click', e => {
                this._setCoords(e.latlng);
                if (this.marker) {
                    this.marker.setLatLng(e.latlng);
                } else {
                    this.marker = L.marker(e.latlng, { icon, draggable: true }).addTo(this.map);
                    this.marker.on('dragend', ev => this._setCoords(ev.target.getLatLng()));
                }
            });

            /* sync from inputs → map */
            ['location_lat', 'location_lng'].forEach(id => {
                document.getElementById(id)?.addEventListener('change', () => this._syncFromInputs());
            });
        },
        _setCoords({ lat, lng }) {
            document.getElementById('location_lat').value = lat.toFixed(7);
            document.getElementById('location_lng').value = lng.toFixed(7);
        },
        _syncFromInputs() {
            const la = parseFloat(document.getElementById('location_lat').value);
            const ln = parseFloat(document.getElementById('location_lng').value);
            if (!isNaN(la) && !isNaN(ln)) {
                if (this.marker) this.marker.setLatLng([la, ln]);
                this.map.setView([la, ln], Math.max(this.map.getZoom(), 11));
            }
        },
    };
}

function itineraryMapPicker(item) {
    return {
        open: false,
        map: null,
        marker: null,
        async toggle(element) {
            this.open = !this.open;

            if (!this.open) {
                return;
            }

            await new Promise(resolve => requestAnimationFrame(resolve));
            await this.init(element);
        },
        async init(element) {
            await ensureLeafletAssets();

            const lat = parseFloat(item.location_lat);
            const lng = parseFloat(item.location_lng);
            const hasCoords = !Number.isNaN(lat) && !Number.isNaN(lng);
            const startLat = hasCoords ? lat : 31.7917;
            const startLng = hasCoords ? lng : -7.0926;
            const zoom = hasCoords ? 12 : 6;

            if (this.map) {
                this.map.invalidateSize();
                return;
            }

            this.map = L.map(element).setView([startLat, startLng], zoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(this.map);

            if (hasCoords) {
                this.marker = L.marker([lat, lng], { icon: experienceMapIcon(), draggable: true }).addTo(this.map);
                this.marker.on('dragend', event => this.setCoords(event.target.getLatLng()));
            }

            this.map.on('click', event => {
                this.setCoords(event.latlng);
                if (this.marker) {
                    this.marker.setLatLng(event.latlng);
                    return;
                }

                this.marker = L.marker(event.latlng, { icon: experienceMapIcon(), draggable: true }).addTo(this.map);
                this.marker.on('dragend', dragEvent => this.setCoords(dragEvent.target.getLatLng()));
            });

            setTimeout(() => this.map.invalidateSize(), 80);
        },
        setCoords({ lat, lng }) {
            item.location_lat = lat.toFixed(7);
            item.location_lng = lng.toFixed(7);
        },
    };
}

/* ── Video preview (Alpine.js) ─────────────────────────────── */
function videoPreview() {
    return {
        embedUrl: null,
        videoUrl: null,
        init(url) {
            if (url) this.preview(url);
        },
        preview(url) {
            url = (url || '').trim();
            this.embedUrl = null;
            this.videoUrl = null;
            if (!url) return;

            /* YouTube */
            const ytMatch = url.match(
                /(?:youtube\.com\/(?:watch\?v=|shorts\/|embed\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/
            );
            if (ytMatch) {
                this.embedUrl = `https://www.youtube-nocookie.com/embed/${ytMatch[1]}?rel=0&modestbranding=1&color=white`;
                return;
            }

            /* Vimeo */
            const vmMatch = url.match(/vimeo\.com\/(?:video\/)?(\d+)/);
            if (vmMatch) {
                this.embedUrl = `https://player.vimeo.com/video/${vmMatch[1]}?title=0&byline=0&portrait=0&color=8b5e48`;
                return;
            }

            /* Direct video file */
            if (/\.(mp4|webm|mov|ogg)(\?|$)/i.test(url)) {
                this.videoUrl = url;
                return;
            }
        },
    };
}

function itineraryBuilder(initialItems) {
    const normalize = (item = {}) => ({
        key: item.key || `${Date.now()}-${Math.random().toString(16).slice(2)}`,
        type: item.type || 'stop',
        icon: item.icon || 'pin',
        title: {
            en: item.title?.en || '',
            fr: item.title?.fr || '',
            nl: item.title?.nl || '',
        },
        description: {
            en: item.description?.en || '',
            fr: item.description?.fr || '',
            nl: item.description?.nl || '',
        },
        duration_minutes: item.duration_minutes || '',
        location_name: {
            en: item.location_name?.en || '',
            fr: item.location_name?.fr || '',
            nl: item.location_name?.nl || '',
        },
        location_lat: item.location_lat || '',
        location_lng: item.location_lng || '',
        is_main_stop: Boolean(item.is_main_stop),
    });

    return {
        items: Array.isArray(initialItems) ? initialItems.map(normalize) : [],
        add() {
            this.items.push(normalize({ type: this.items.length === 0 ? 'start' : 'stop' }));
        },
        remove(index) {
            this.items.splice(index, 1);
        },
    };
}

/* ── Photo previews ─────────────────────────────────────────── */
(function () {
    const input = document.getElementById('photo-input');
    const grid  = document.getElementById('photo-previews');
    const zone  = document.getElementById('photo-drop-zone');
    if (!input || !grid) return;

    input.addEventListener('change', () => renderPreviews(input.files));

    /* drag-and-drop */
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('xp-file-drop--active'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('xp-file-drop--active'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        zone.classList.remove('xp-file-drop--active');
        const dt = e.dataTransfer;
        /* merge into input */
        const arr = Array.from(dt.files).filter(f => f.type.startsWith('image/'));
        const merged = mergeFileLists(input.files, arr);
        setInputFiles(merged);
        renderPreviews(merged);
    });

    function renderPreviews(files) {
        grid.innerHTML = '';
        if (!files || !files.length) return;
        Array.from(files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = ev => {
                const wrap = document.createElement('div');
                wrap.className = 'xp-photo-thumb';
                wrap.innerHTML = `
                    <img src="${ev.target.result}" alt="">
                    <button type="button" class="xp-photo-remove" data-idx="${i}" title="Remove">×</button>
                `;
                wrap.querySelector('.xp-photo-remove').addEventListener('click', () => removeFile(i));
                grid.appendChild(wrap);
            };
            reader.readAsDataURL(file);
        });
    }

    function removeFile(idx) {
        const arr = Array.from(input.files).filter((_, i) => i !== idx);
        setInputFiles(arr);
        renderPreviews(arr);
    }

    function setInputFiles(arr) {
        const dt = new DataTransfer();
        arr.forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    function mergeFileLists(existing, incoming) {
        const arr = Array.from(existing || []);
        incoming.forEach(f => {
            if (!arr.some(e => e.name === f.name && e.size === f.size)) arr.push(f);
        });
        return arr;
    }
})();
</script>
@endpush

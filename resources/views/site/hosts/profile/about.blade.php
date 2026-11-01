<section class="flex flex-col gap-5" aria-labelledby="about-title">
    <x-site.atoms.eyebrow>{{ __('hosts.profile.story') }}</x-site.atoms.eyebrow>
    <h2 id="about-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('hosts.profile.about', ['name' => $page->name]) }}</h2>
    @foreach($page->story as $paragraph)
        <p @class(['lv-t-body-l', 'lv-tone-ink' => $loop->first, 'lv-tone-muted' => ! $loop->first])>{{ $paragraph }}</p>
    @endforeach
</section>

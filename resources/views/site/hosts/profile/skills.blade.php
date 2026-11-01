<section class="flex flex-col gap-5" aria-labelledby="skills-title">
    <h2 id="skills-title" class="lv-t-h2 lv-serif lv-tone-ink">{{ __('hosts.profile.knows', ['name' => $page->name]) }}</h2>
    <ul class="flex flex-wrap gap-3">
        @foreach($page->skills as $skill)
            <li class="lv-hp-skill lv-t-ui-s lv-tone-ink">{{ $skill }}</li>
        @endforeach
    </ul>
</section>

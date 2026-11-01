{{-- Practical Morocco — same "Know before you go" panel as the landing. --}}
<div class="relative overflow-hidden pt-16 lg:pt-[100px] lv-bg-cream">
    <x-site.atoms.ornament class="lv-ornament-leaves--center" data-parallax="16" />
    <x-site.organisms.know-before :faqs="$page->faqs" :link-href="$page->urls->experiences()" class="relative" />
</div>

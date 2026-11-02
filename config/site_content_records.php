<?php

use App\Domain\Discover\Models\Host;
use App\Domain\Experience\Models\Experience;
use App\Domain\Landing\Models\Destination;
use App\Domain\Landing\Models\ExperienceCategory;
use App\Domain\Landing\Models\LandingFaq;
use App\Domain\Landing\Models\Testimonial;

return [
    'experiences' => ['model' => Experience::class, 'index' => 'admin.experiences.index', 'edit' => 'admin.experiences.edit', 'parameter' => 'experience', 'label' => 'Experiences'],
    'hosts' => ['model' => Host::class, 'index' => 'admin.hosts.index', 'edit' => 'admin.hosts.edit', 'parameter' => 'host', 'label' => 'Local hosts'],
    'categories' => ['model' => ExperienceCategory::class, 'index' => 'admin.landing.categories.index', 'view' => 'admin.landing.categories', 'label' => 'Categories'],
    'destinations' => ['model' => Destination::class, 'index' => 'admin.landing.destinations.index', 'view' => 'admin.landing.destinations', 'label' => 'Destinations'],
    'faqs' => ['model' => LandingFaq::class, 'index' => 'admin.landing.faqs.index', 'view' => 'admin.landing.faqs', 'label' => 'FAQs'],
    'testimonials' => ['model' => Testimonial::class, 'index' => 'admin.landing.testimonials.index', 'view' => 'admin.landing.testimonials', 'label' => 'Testimonials'],
];

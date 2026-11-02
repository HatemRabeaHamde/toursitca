<script type="application/ld+json">
{{-- JSON-LD is generated from validated fields; HEX flags prevent script-tag injection. --}}
{!! json_encode($seo['schema'] ?? [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => config('seo.organization.name'),
    'url' => config('seo.organization.url'),
    'logo' => config('seo.organization.logo'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}
</script>

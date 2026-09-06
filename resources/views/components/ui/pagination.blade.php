@props(['paginator'])

@if ($paginator->hasPages())
    <div {{ $attributes->merge(['class' => 'mt-6']) }}>
        {{ $paginator->links() }}
    </div>
@endif

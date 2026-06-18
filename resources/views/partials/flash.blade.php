@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if (session($type))
        <div class="rounded-md border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 shadow-sm" role="status">
            {{ session($type) }}
        </div>
    @endif
@endforeach

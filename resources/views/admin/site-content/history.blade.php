@if($field['revisions']->isNotEmpty())
<details class="mb-3 rounded border bg-white p-4">
 <summary>Revision history — {{ $field['label'] }}</summary>
 @foreach($field['revisions'] as $revision)
 <form method="POST" action="{{ route('admin.site-content.restore', [$page, $revision->id]) }}" class="mt-3 flex flex-wrap items-center gap-3">
  @csrf <span>{{ $revision->created_at }} · User {{ $revision->changed_by }}</span>
  <button class="rounded border px-3 py-2">Restore to draft</button>
 </form>
 @endforeach
</details>
@endif

@php($current = $field['translations']['en']['url'] ?? $field['defaults']['en']['url'])
<div @site-content-image-default.window="if ($event.detail.key === @js($field['key']) && !image) image = $event.detail.url" class="rounded border border-dashed p-4" @dragover.prevent @drop.prevent="const files = $event.dataTransfer.files; if (files.length) { $refs.upload.files = files; image = URL.createObjectURL(files[0]); $refs.upload.dispatchEvent(new Event('input', { bubbles: true })); dirty = true; }">
 <img x-show="image || @js($current)" :src="image || @js($current)" alt="Current image preview" class="mb-3 max-h-44 max-w-full rounded object-contain">
 <label class="block text-sm" for="{{ $field['encoded'] }}-upload">Drop an image here or choose a file</label>
 <input id="{{ $field['encoded'] }}-upload" x-ref="upload" type="file" name="{{ $base }}[upload]" accept="image/jpeg,image/png,image/webp" @change="if ($event.target.files[0]) image = URL.createObjectURL($event.target.files[0])" class="my-3 w-full">
 <label class="flex gap-2"><input type="checkbox" name="{{ $base }}[remove]" value="1">Remove image when saved</label>
</div>
@foreach(['en','fr','nl'] as $language)
 <label class="mt-3 block text-sm" for="{{ $field['encoded'] }}-alt-{{ $language }}">Image description ({{ strtoupper($language) }})</label>
 <input class="w-full rounded border p-3" id="{{ $field['encoded'] }}-alt-{{ $language }}" name="{{ $base }}[alt][{{ $language }}]" maxlength="250" value="{{ old('fields.'.$field['encoded'].'.alt.'.$language, $field['translations'][$language]['alt'] ?? '') }}">
@endforeach

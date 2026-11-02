@php($base = 'fields['.$field['encoded'].']')
<div class="mb-8 border-b pb-6" x-data="{ language: 'en', image: '' }" x-effect="if (typeof locale !== 'undefined') language = locale">
 <h3 class="font-semibold">{{ $field['label'] }}</h3>
 <p class="mb-3 text-sm text-gray-600">{{ $field['hint'] }}</p>
 <label class="mb-3 flex items-center gap-2 text-sm"><input type="checkbox" name="{{ $base }}[reset]" value="1">Reset to default when saved</label>
 @if($field['type'] === 'image')
  @include('admin.site-content.image', ['base'=>$base])
 @else
  <div role="tablist" aria-label="{{ $field['label'] }} language" class="mb-3 flex gap-2">
   @foreach(['en','fr','nl'] as $language)
    <button type="button" role="tab" :aria-selected="language === '{{ $language }}'" @click="language = '{{ $language }}'" class="rounded border px-3 py-2">{{ strtoupper($language) }} {{ empty($field['translations'][$language]) ? '· Empty' : '' }}</button>
   @endforeach
  </div>
  @foreach(['en','fr','nl'] as $language)
   <div x-show="language === '{{ $language }}'" role="tabpanel">
    <label class="block text-sm" for="{{ $field['encoded'] }}-{{ $language }}">{{ $field['label'] }} ({{ strtoupper($language) }})</label>
    @if($field['type'] === 'richtext')
     @include('admin.site-content.richtext')
    @elseif($field['type'] === 'textarea')
     <textarea class="w-full rounded border p-3" rows="4" id="{{ $field['encoded'] }}-{{ $language }}" name="{{ $base }}[text][{{ $language }}]" maxlength="{{ $field['max'] }}" placeholder="{{ $field['defaults'][$language] }}">{{ old('fields.'.$field['encoded'].'.text.'.$language, $field['translations'][$language] ?? '') }}</textarea>
    @elseif(isset($field['allowed']))
     <select class="w-full rounded border p-3" id="{{ $field['encoded'] }}-{{ $language }}" name="{{ $base }}[text][{{ $language }}]"><option value="">Default</option>@foreach($field['allowed'] as $option)<option value="{{ $option }}" @selected(old('fields.'.$field['encoded'].'.text.'.$language, $field['translations'][$language] ?? '') === $option)>{{ $option }}</option>@endforeach</select>
    @else
     <input class="w-full rounded border p-3" id="{{ $field['encoded'] }}-{{ $language }}" name="{{ $base }}[text][{{ $language }}]" maxlength="{{ $field['max'] }}" value="{{ old('fields.'.$field['encoded'].'.text.'.$language, $field['translations'][$language] ?? '') }}" placeholder="{{ $field['defaults'][$language] }}">
    @endif
   </div>
  @endforeach
 @endif
</div>

@props(['field'])
@error($field)
<p class="text-red-mark text-xs mt-1">{{ $message }}</p>
@enderror

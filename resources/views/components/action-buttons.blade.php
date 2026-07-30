@props(['align' => 'right'])

<div class="flex gap-2 {{ $align === 'right' ? 'justify-end' : 'justify-start' }}">
    {{ $slot }}
</div>

<?php

if (!function_exists('locale_label')) {
    function locale_label(string $locale): string
    {
        return match ($locale) {
            'en' => 'English',
            'ja' => '日本語',
            'id' => 'Indonesia',
            default => 'English',
        };
    }
}

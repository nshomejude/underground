@props([
    'title',
    'eyebrow' => 'Content Admin',
    'maxWidth' => 'max-w-5xl',
])

<x-admin.layout :title="$title" :eyebrow="$eyebrow" :max-width="$maxWidth">
    {{ $slot }}
</x-admin.layout>

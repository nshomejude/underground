@props([
    'title',
    'eyebrow' => 'Content Admin',
    'description' => null,
    'maxWidth' => 'max-w-7xl',
])

<x-admin.layout :title="$title" :eyebrow="$eyebrow" :description="$description" :max-width="$maxWidth">
    @isset($actions)
        <x-slot:actions>{{ $actions }}</x-slot:actions>
    @endisset

    {{ $slot }}
</x-admin.layout>

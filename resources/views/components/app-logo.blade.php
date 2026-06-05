@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="SIPAS-Hub" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center text-primary">
            <x-app-logo-icon class="size-6 text-primary" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="SIPAS-Hub" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center text-primary">
            <x-app-logo-icon class="size-6 text-primary" />
        </x-slot>
    </flux:brand>
@endif

@props([
    'offering',
    'url',
])

<x-filament::section>
    <div class="flex h-full flex-col gap-4">
        <div class="flex items-start gap-4">
            <div class="bg-primary-50 text-primary-600 dark:bg-primary-500/10 flex h-11 w-11 shrink-0 items-center justify-center rounded-lg">
                <x-filament::icon :icon="$offering->icon ?: 'heroicon-o-briefcase'" class="h-6 w-6" />
            </div>

            <div class="min-w-0">
                <h3 class="font-semibold text-gray-950 dark:text-white">{{ $offering->name }}</h3>
            </div>
        </div>

        @if ($offering->description)
            <p class="grow text-sm text-gray-600 dark:text-gray-300">{{ $offering->description }}</p>
        @endif

        <x-filament::button :href="$url" tag="a" icon="heroicon-m-arrow-right" icon-position="after">
            Start request
        </x-filament::button>
    </div>
</x-filament::section>

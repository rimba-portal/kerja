<x-filament-panels::page>
    <div class="space-y-6">
        <div class="max-w-xl">
            <x-filament::input.wrapper prefix-icon="heroicon-m-magnifying-glass">
                <x-filament::input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search available offerings"
                />
            </x-filament::input.wrapper>
        </div>

        @if ($this->offerings->isEmpty())
            <x-filament::section>
                <div class="py-8 text-center">
                    <x-filament::icon icon="heroicon-o-inbox" class="mx-auto h-10 w-10 text-gray-400" />

                    <h3 class="mt-3 font-semibold">No offerings available</h3>

                    <p class="mt-1 text-sm text-gray-500">
                        There are currently no services available for you to request.
                    </p>
                </div>
            </x-filament::section>
        @else
            <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($this->offerings as $offering)
                    <x-work::offering-card :offering="$offering" :url="$this->offeringUrl($offering)" />
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>

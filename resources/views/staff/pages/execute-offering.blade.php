<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <x-filament::section>
                <livewire:work.dynamic-offering-form
                    :offering="$offering"
                    wire:key="offering-form-{{ $offering->getKey() }}"
                />
            </x-filament::section>
        </div>

        <div class="lg:col-span-4">
            <x-filament::section heading="What happens next?" icon="heroicon-o-information-circle">
                <div class="space-y-4 text-sm">
                    <p>Your submitted information creates the related business record and starts its workflow.</p>

                    @if ($offering->workflow)
                        <div>
                            <div class="font-semibold">Workflow</div>

                            <div class="text-gray-500">{{ $offering->workflow->name }}</div>
                        </div>
                    @endif
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>

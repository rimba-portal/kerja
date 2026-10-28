<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-12">
        <div class="space-y-6 lg:col-span-8">
            <x-filament::section heading="Request">
                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd class="font-medium">{{ str($execution->status->value)->headline() }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Submitted</dt>
                        <dd class="font-medium">{{ $execution->created_at?->format('d M Y H:i') }}</dd>
                    </div>
                </dl>
            </x-filament::section>

            @if ($execution->subject)
                <x-filament::section heading="Business record">
                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-gray-500">Type</dt>
                            <dd class="font-medium">{{ class_basename($execution->subject) }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Identifier</dt>
                            <dd class="font-medium">{{ $execution->subject->getKey() }}</dd>
                        </div>
                    </dl>
                </x-filament::section>
            @endif

            @if ($execution->failure_message)
                <x-filament::section heading="Failure" icon="heroicon-o-exclamation-triangle">
                    <p class="text-danger-600">{{ $execution->failure_message }}</p>
                </x-filament::section>
            @endif
        </div>

        <div class="space-y-6 lg:col-span-4">
            <x-filament::section heading="Workflow">
                @if ($execution->workflowInstance)
                    <p class="font-medium">
                        {{
                            data_get(
                                $execution->workflowInstance,
                                'status',
                                'Running',
                            )
                        }}
                    </p>
                @else
                    <p class="text-sm text-gray-500">No workflow run is linked yet.</p>
                @endif
            </x-filament::section>

            <x-filament::section heading="Timeline">
                <div class="space-y-3 text-sm">
                    <div>Created {{ $execution->created_at?->diffForHumans() }}</div>

                    @if ($execution->started_at)
                        <div>Started {{ $execution->started_at->diffForHumans() }}</div>
                    @endif

                    @if ($execution->completed_at)
                        <div>Completed {{ $execution->completed_at->diffForHumans() }}</div>
                    @endif
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>

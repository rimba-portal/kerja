<div>
    <livewire:form-builder
        :form="$providerForm"
        :values="$values"
        :preview="$preview"
        wire:key="work-form-{{ $offering->getKey() }}"
    />
</div>

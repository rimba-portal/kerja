<?php

declare(strict_types=1);

namespace Rimba\Work\Services;

use Rimba\Work\Contracts\OfferingInputMapper;
use Rimba\Work\Data\MappedOfferingData;
use Rimba\Work\Data\OfferingContext;
use Rimba\Work\Exceptions\InvalidFormMappingException;
use Rimba\Work\Models\Offering;

class OfferingMappingService implements OfferingInputMapper
{
    public function map(
        Offering $offering,
        array $input,
        OfferingContext $context,
    ): MappedOfferingData {
        $contextData = $context->toArray();

        $attributes = $offering->defaults ?? [];

        $attributes = array_replace_recursive(
            $attributes,
            $this->applyMap(
                source: $input,
                mapping: $offering->input_mapping ?? [],
                sourceName: 'input',
            ),
        );

        /*
         * Trusted context is applied last. It overrides submitted values.
         */
        $attributes = array_replace_recursive(
            $attributes,
            $this->applyMap(
                source: $contextData,
                mapping: $offering->context_mapping ?? [],
                sourceName: 'context',
            ),
        );

        return new MappedOfferingData(
            attributes: $attributes,
            input: $input,
            context: $contextData,
        );
    }

    protected function applyMap(
        array $source,
        array $mapping,
        string $sourceName,
    ): array {
        $result = [];

        foreach ($mapping as $sourcePath => $targetPath) {
            if (
                ! is_string($sourcePath)
                || blank($sourcePath)
                || ! is_string($targetPath)
                || blank($targetPath)
            ) {
                throw new InvalidFormMappingException(
                    "Invalid {$sourceName} mapping entry."
                );
            }

            if (! data_has($source, $sourcePath)) {
                continue;
            }

            data_set(
                $result,
                $targetPath,
                data_get($source, $sourcePath),
            );
        }

        return $result;
    }
}

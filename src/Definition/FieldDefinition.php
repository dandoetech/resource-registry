<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

final class FieldDefinition
{
    public function __construct(
        public string    $name,
        public FieldType $type,
        public bool      $nullable = true,
        /** @var list<string> */
        public array     $rules = [],
        public ?string   $label = null,
        public ?string   $description = null,
    ) {
    }
}

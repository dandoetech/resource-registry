<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

final class ResourceDefinition
{
    /**
     * @param non-empty-string $key
     * @param list<FieldDefinition> $fields
     * @param list<RelationDefinition> $relations
     * @param list<ActionDefinition> $actions
     */
    public function __construct(
        public string  $key,
        public string  $label,
        public array   $fields = [],
        public array   $relations = [],
        public array   $actions = [],
        public ?string $description = null,
    )
    {
    }
}

<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

final class RelationDefinition
{
    public function __construct(
        public string       $name,
        public RelationType $type,
        /** Target resource key (from registry) */
        public string       $target,
        public ?string      $label = null,
        public ?string      $description = null,
    )
    {
    }
}

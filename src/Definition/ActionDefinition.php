<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

final class ActionDefinition
{
    public function __construct(
        public string  $name,
        public ?string $description = null,
    ) {
    }
}

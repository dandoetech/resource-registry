<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;

/** Registry drivers provide resource metadata from various backends. */
interface RegistryDriverInterface
{
    /** @return list<ResourceDefinition> */
    public function all(): array;

    public function find(string $key): ?ResourceDefinition;
}

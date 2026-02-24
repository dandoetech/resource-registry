<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

/** Registry drivers provide resource metadata from various backends. */
interface RegistryDriverInterface
{
    /** @return list<ResourceDefinitionInterface> */
    public function all(): array;

    public function find(string $key): ?ResourceDefinitionInterface;
}

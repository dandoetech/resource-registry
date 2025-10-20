<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Registry;

use DanDoeTech\ResourceRegistry\Contracts\RegistryDriverInterface;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;

final readonly class Registry
{
    public function __construct(private RegistryDriverInterface $driver)
    {
    }

    /** @return list<ResourceDefinition> */
    public function all(): array
    {
        return $this->driver->all();
    }

    public function getResource(string $key): ?ResourceDefinition
    {
        return $this->driver->find($key);
    }
}

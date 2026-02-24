<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Registry;

use DanDoeTech\ResourceRegistry\Contracts\RegistryDriverInterface;
use DanDoeTech\ResourceRegistry\Contracts\ResourceDefinitionInterface;

final readonly class Registry
{
    public function __construct(private RegistryDriverInterface $driver)
    {
    }

    /** @return list<ResourceDefinitionInterface> */
    public function all(): array
    {
        return $this->driver->all();
    }

    public function getResource(string $key): ?ResourceDefinitionInterface
    {
        return $this->driver->find($key);
    }
}

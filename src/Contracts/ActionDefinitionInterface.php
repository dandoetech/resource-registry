<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

interface ActionDefinitionInterface
{
    public function getName(): string;

    public function getDescription(): ?string;

    /** @return array<string, mixed> */
    public function getMeta(): array;

    /**
     * Class-string of the action handler for custom (non-CRUD) actions.
     * Returns null for standard CRUD actions (create, update, delete)
     * which are handled by the GenericController directly.
     */
    public function getHandler(): ?string;
}

<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

interface ActionDefinitionInterface
{
    public function getName(): string;

    public function getDescription(): ?string;

    /** @return array<string, mixed> */
    public function getMeta(): array;
}

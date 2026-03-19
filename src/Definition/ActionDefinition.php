<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ActionDefinitionInterface;

final class ActionDefinition implements ActionDefinitionInterface
{
    /**
     * @param array<string, mixed> $meta
     * @param ?string              $handler Class-string of action handler for custom actions (null for CRUD)
     */
    public function __construct(
        public string  $name,
        public ?string $description = null,
        public array   $meta = [],
        public ?string $handler = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getHandler(): ?string
    {
        return $this->handler;
    }
}

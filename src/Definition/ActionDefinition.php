<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ActionDefinitionInterface;

final class ActionDefinition implements ActionDefinitionInterface
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string  $name,
        public ?string $description = null,
        public array   $meta = [],
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
}

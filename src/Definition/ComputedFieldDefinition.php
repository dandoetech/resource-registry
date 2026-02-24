<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ComputedFieldDefinitionInterface;

final class ComputedFieldDefinition implements ComputedFieldDefinitionInterface
{
    public function __construct(
        public string    $name,
        public FieldType $type,
        /** e.g. 'category.name', 'count:orders', or null if custom resolver */
        public ?string   $via = null,
        /** FQCN of a resolver class, or null if via is sufficient */
        public ?string   $resolver = null,
        public ?string   $label = null,
        public ?string   $description = null,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function getVia(): ?string
    {
        return $this->via;
    }

    public function getResolver(): ?string
    {
        return $this->resolver;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}

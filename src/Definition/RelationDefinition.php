<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

use DanDoeTech\ResourceRegistry\Contracts\RelationDefinitionInterface;

final class RelationDefinition implements RelationDefinitionInterface
{
    /**
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string       $name,
        public RelationType $type,
        /** Target resource key (from registry) */
        public string       $target,
        public ?string      $label = null,
        public ?string      $description = null,
        public ?string      $foreignKey = null,
        public ?string      $relatedKey = null,
        public ?string      $pivotTable = null,
        public array        $meta = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): RelationType
    {
        return $this->type;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getForeignKey(): ?string
    {
        return $this->foreignKey;
    }

    public function getRelatedKey(): ?string
    {
        return $this->relatedKey;
    }

    public function getPivotTable(): ?string
    {
        return $this->pivotTable;
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        return $this->meta;
    }
}

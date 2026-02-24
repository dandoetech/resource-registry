<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry;

use DanDoeTech\ResourceRegistry\Builder\ResourceBuilder;
use DanDoeTech\ResourceRegistry\Contracts\ActionDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\ComputedFieldDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\FieldDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\RelationDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\ResourceDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;

abstract class Resource implements ResourceDefinitionInterface
{
    private ?ResourceDefinition $definition = null;

    abstract protected function define(ResourceBuilder $builder): void;

    public function getKey(): string
    {
        return $this->resolve()->getKey();
    }

    public function getVersion(): int
    {
        return $this->resolve()->getVersion();
    }

    public function getLabel(): string
    {
        return $this->resolve()->getLabel();
    }

    /** @return list<FieldDefinitionInterface> */
    public function getFields(): array
    {
        return $this->resolve()->getFields();
    }

    public function getField(string $name): ?FieldDefinitionInterface
    {
        return $this->resolve()->getField($name);
    }

    /** @return list<RelationDefinitionInterface> */
    public function getRelations(): array
    {
        return $this->resolve()->getRelations();
    }

    /** @return list<ActionDefinitionInterface> */
    public function getActions(): array
    {
        return $this->resolve()->getActions();
    }

    /** @return list<ComputedFieldDefinitionInterface> */
    public function getComputedFields(): array
    {
        return $this->resolve()->getComputedFields();
    }

    public function isTimestamped(): bool
    {
        return $this->resolve()->isTimestamped();
    }

    public function usesSoftDeletes(): bool
    {
        return $this->resolve()->usesSoftDeletes();
    }

    /** @return list<string> */
    public function getFilterable(): array
    {
        return $this->resolve()->getFilterable();
    }

    /** @return list<string> */
    public function getSortable(): array
    {
        return $this->resolve()->getSortable();
    }

    /** @return list<string> */
    public function getSearchable(): array
    {
        return $this->resolve()->getSearchable();
    }

    public function getDescription(): ?string
    {
        return $this->resolve()->getDescription();
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        return $this->resolve()->getMeta();
    }

    private function resolve(): ResourceDefinition
    {
        if ($this->definition === null) {
            $builder = new ResourceBuilder();
            $this->define($builder);
            $this->definition = $builder->build();
        }

        return $this->definition;
    }
}

<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ActionDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\ComputedFieldDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\FieldDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\RelationDefinitionInterface;
use DanDoeTech\ResourceRegistry\Contracts\ResourceDefinitionInterface;

final class ResourceDefinition implements ResourceDefinitionInterface
{
    /**
     * @param non-empty-string              $key
     * @param list<FieldDefinition>         $fields
     * @param list<RelationDefinition>      $relations
     * @param list<ActionDefinition>        $actions
     * @param list<ComputedFieldDefinition> $computedFields
     * @param list<string>                  $filterable
     * @param list<string>                  $sortable
     * @param list<string>                  $searchable
     * @param array<string, mixed>          $meta
     */
    public function __construct(
        public string  $key,
        public string  $label,
        public array   $fields = [],
        public array   $relations = [],
        public array   $actions = [],
        public ?string $description = null,
        public int     $version = 1,
        public bool    $timestamps = false,
        public bool    $softDeletes = false,
        public array   $computedFields = [],
        public array   $filterable = [],
        public array   $sortable = [],
        public array   $searchable = [],
        public array   $meta = [],
        public ?string $routeSegment = null,
    ) {
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /** @return list<FieldDefinitionInterface> */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(string $name): ?FieldDefinitionInterface
    {
        foreach ($this->fields as $field) {
            if ($field->getName() === $name) {
                return $field;
            }
        }

        return null;
    }

    /** @return list<RelationDefinitionInterface> */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /** @return list<ActionDefinitionInterface> */
    public function getActions(): array
    {
        return $this->actions;
    }

    /** @return list<ComputedFieldDefinitionInterface> */
    public function getComputedFields(): array
    {
        return $this->computedFields;
    }

    public function isTimestamped(): bool
    {
        return $this->timestamps;
    }

    public function usesSoftDeletes(): bool
    {
        return $this->softDeletes;
    }

    /** @return list<string> */
    public function getFilterable(): array
    {
        return $this->filterable;
    }

    /** @return list<string> */
    public function getSortable(): array
    {
        return $this->sortable;
    }

    /** @return list<string> */
    public function getSearchable(): array
    {
        return $this->searchable;
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

    public function getRouteSegment(): ?string
    {
        return $this->routeSegment;
    }
}

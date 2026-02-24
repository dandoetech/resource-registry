<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Builder;

use DanDoeTech\ResourceRegistry\Definition\ActionDefinition;
use DanDoeTech\ResourceRegistry\Definition\ComputedFieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationDefinition;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;

final class ResourceBuilder
{
    private ?string $key = null;

    private int $version = 1;

    private ?string $label = null;

    private ?string $description = null;

    private bool $timestamps = false;

    private bool $softDeletes = false;

    /** @var list<FieldDefinition> */
    private array $fields = [];

    /** @var list<RelationDefinition> */
    private array $relations = [];

    /** @var list<ActionDefinition> */
    private array $actions = [];

    /** @var list<ComputedFieldDefinition> */
    private array $computedFields = [];

    /** @var list<string> */
    private array $filterable = [];

    /** @var list<string> */
    private array $sortable = [];

    /** @var list<string> */
    private array $searchable = [];

    /** @var array<string, mixed> */
    private array $meta = [];

    /** @var array<string, true> */
    private array $fieldNames = [];

    /** @var array<string, true> */
    private array $relationNames = [];

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function version(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function timestamps(): self
    {
        $this->timestamps = true;

        return $this;
    }

    public function softDeletes(): self
    {
        $this->softDeletes = true;

        return $this;
    }

    /**
     * @param list<string>         $rules
     * @param array<string, mixed> $meta
     */
    public function field(
        string $name,
        FieldType $type,
        bool $nullable = true,
        array $rules = [],
        ?string $label = null,
        ?string $description = null,
        bool $unique = false,
        bool $indexed = false,
        mixed $default = null,
        ?string $comment = null,
        array $meta = [],
    ): self {
        if (isset($this->fieldNames[$name])) {
            throw new \InvalidArgumentException("Duplicate field name: '{$name}'");
        }

        $this->fieldNames[$name] = true;
        $this->fields[] = new FieldDefinition(
            name: $name,
            type: $type,
            nullable: $nullable,
            rules: $rules,
            label: $label,
            description: $description,
            unique: $unique,
            indexed: $indexed,
            default: $default,
            comment: $comment,
            meta: $meta,
        );

        return $this;
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function belongsTo(
        string $name,
        ?string $target = null,
        ?string $foreignKey = null,
        ?string $relatedKey = null,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        return $this->addRelation(
            type: RelationType::BelongsTo,
            name: $name,
            target: $target ?? $name,
            foreignKey: $foreignKey,
            relatedKey: $relatedKey,
            label: $label,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function hasMany(
        string $name,
        ?string $target = null,
        ?string $foreignKey = null,
        ?string $relatedKey = null,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        return $this->addRelation(
            type: RelationType::HasMany,
            name: $name,
            target: $target ?? $name,
            foreignKey: $foreignKey,
            relatedKey: $relatedKey,
            label: $label,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function hasOne(
        string $name,
        ?string $target = null,
        ?string $foreignKey = null,
        ?string $relatedKey = null,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        return $this->addRelation(
            type: RelationType::HasOne,
            name: $name,
            target: $target ?? $name,
            foreignKey: $foreignKey,
            relatedKey: $relatedKey,
            label: $label,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function belongsToMany(
        string $name,
        ?string $target = null,
        ?string $foreignKey = null,
        ?string $relatedKey = null,
        ?string $pivotTable = null,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        return $this->addRelation(
            type: RelationType::BelongsToMany,
            name: $name,
            target: $target ?? $name,
            foreignKey: $foreignKey,
            relatedKey: $relatedKey,
            pivotTable: $pivotTable,
            label: $label,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function morphTo(
        string $name,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        return $this->addRelation(
            type: RelationType::MorphTo,
            name: $name,
            target: $name,
            label: $label,
            description: $description,
            meta: $meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function morphMany(
        string $name,
        ?string $target = null,
        ?string $foreignKey = null,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        return $this->addRelation(
            type: RelationType::MorphMany,
            name: $name,
            target: $target ?? $name,
            foreignKey: $foreignKey,
            label: $label,
            description: $description,
            meta: $meta,
        );
    }

    public function computed(
        string $name,
        FieldType $type,
        ?string $via = null,
        ?string $resolver = null,
        ?string $label = null,
        ?string $description = null,
    ): self {
        $this->computedFields[] = new ComputedFieldDefinition(
            name: $name,
            type: $type,
            via: $via,
            resolver: $resolver,
            label: $label,
            description: $description,
        );

        return $this;
    }

    /** @param list<string> $fields */
    public function filterable(array $fields): self
    {
        $this->filterable = $fields;

        return $this;
    }

    /** @param list<string> $fields */
    public function sortable(array $fields): self
    {
        $this->sortable = $fields;

        return $this;
    }

    /** @param list<string> $fields */
    public function searchable(array $fields): self
    {
        $this->searchable = $fields;

        return $this;
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function action(string $name, ?string $description = null, array $meta = []): self
    {
        $this->actions[] = new ActionDefinition(
            name: $name,
            description: $description,
            meta: $meta,
        );

        return $this;
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function meta(array $meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    public function build(): ResourceDefinition
    {
        if ($this->key === null || $this->key === '') {
            throw new \InvalidArgumentException('Resource key is required');
        }

        return new ResourceDefinition(
            key: $this->key,
            label: $this->label ?? \ucwords(\str_replace('_', ' ', $this->key)),
            fields: $this->fields,
            relations: $this->relations,
            actions: $this->actions,
            description: $this->description,
            version: $this->version,
            timestamps: $this->timestamps,
            softDeletes: $this->softDeletes,
            computedFields: $this->computedFields,
            filterable: $this->filterable,
            sortable: $this->sortable,
            searchable: $this->searchable,
            meta: $this->meta,
        );
    }

    /**
     * @param array<string, mixed> $meta
     */
    private function addRelation(
        RelationType $type,
        string $name,
        string $target,
        ?string $foreignKey = null,
        ?string $relatedKey = null,
        ?string $pivotTable = null,
        ?string $label = null,
        ?string $description = null,
        array $meta = [],
    ): self {
        if (isset($this->relationNames[$name])) {
            throw new \InvalidArgumentException("Duplicate relation name: '{$name}'");
        }

        $this->relationNames[$name] = true;
        $this->relations[] = new RelationDefinition(
            name: $name,
            type: $type,
            target: $target,
            label: $label,
            description: $description,
            foreignKey: $foreignKey,
            relatedKey: $relatedKey,
            pivotTable: $pivotTable,
            meta: $meta,
        );

        return $this;
    }
}

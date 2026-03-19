<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Registry;

use DanDoeTech\ResourceRegistry\Contracts\RegistryDriverInterface;
use DanDoeTech\ResourceRegistry\Contracts\ResourceDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\ActionDefinition;
use DanDoeTech\ResourceRegistry\Definition\ComputedFieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationDefinition;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;

final class ArrayRegistryDriver implements RegistryDriverInterface
{
    /** @param array<string, array<string, mixed>> $config */
    public function __construct(private readonly array $config)
    {
    }

    /** @return list<ResourceDefinitionInterface> */
    public function all(): array
    {
        $out = [];
        foreach ($this->config as $key => $item) {
            $out[] = $this->hydrate($key, $item);
        }

        return $out;
    }

    public function find(string $key): ?ResourceDefinitionInterface
    {
        if (!isset($this->config[$key])) {
            return null;
        }

        return $this->hydrate($key, $this->config[$key]);
    }

    /** @param array<string, mixed> $item */
    private function hydrate(string $key, array $item): ResourceDefinition
    {
        if ($key === '') {
            throw new \InvalidArgumentException('Resource key must not be empty');
        }

        $label = \is_string($item['label'] ?? null) ? $item['label'] : \ucwords(\str_replace('_', ' ', $key));
        $description = \is_string($item['description'] ?? null) ? $item['description'] : null;
        $version = \is_int($item['version'] ?? null) ? $item['version'] : 1;

        return new ResourceDefinition(
            key: $key,
            label: $label,
            fields: $this->hydrateFields($item),
            relations: $this->hydrateRelations($item),
            actions: $this->hydrateActions($item),
            description: $description,
            version: $version,
            timestamps: !empty($item['timestamps']),
            softDeletes: !empty($item['softDeletes']),
            computedFields: $this->hydrateComputedFields($item),
            filterable: $this->toStringList($item['filterable'] ?? []),
            sortable: $this->toStringList($item['sortable'] ?? []),
            searchable: $this->toStringList($item['searchable'] ?? []),
            meta: $this->toStringKeyedArray($item['meta'] ?? []),
        );
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<FieldDefinition>
     */
    private function hydrateFields(array $item): array
    {
        $raw = $item['fields'] ?? [];
        if (!\is_array($raw)) {
            return [];
        }

        $fields = [];

        foreach ($raw as $f) {
            if ($f instanceof FieldDefinition) {
                $fields[] = $f;

                continue;
            }
            if (!\is_array($f)) {
                throw new \InvalidArgumentException('Each field entry must be a FieldDefinition or array');
            }
            /** @var array<string, mixed> $f */
            $fields[] = $this->hydrateField($f);
        }

        return $fields;
    }

    /**
     * @param array<string, mixed> $f
     */
    private function hydrateField(array $f): FieldDefinition
    {
        $name = $f['name'] ?? null;
        if (!\is_string($name) || $name === '') {
            throw new \InvalidArgumentException('field.name required');
        }

        $type = $f['type'] ?? null;
        $fieldType = $type instanceof FieldType ? $type : FieldType::from(\is_string($type) ? $type : '');

        return new FieldDefinition(
            name: $name,
            type: $fieldType,
            nullable: !empty($f['nullable'] ?? true),
            rules: $this->toStringList($f['rules'] ?? []),
            label: \is_string($f['label'] ?? null) ? $f['label'] : null,
            description: \is_string($f['description'] ?? null) ? $f['description'] : null,
            unique: !empty($f['unique']),
            indexed: !empty($f['indexed']),
            default: $f['default'] ?? null,
            comment: \is_string($f['comment'] ?? null) ? $f['comment'] : null,
            meta: $this->toStringKeyedArray($f['meta'] ?? []),
        );
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<RelationDefinition>
     */
    private function hydrateRelations(array $item): array
    {
        $raw = $item['relations'] ?? [];
        if (!\is_array($raw)) {
            return [];
        }

        $relations = [];

        foreach ($raw as $r) {
            if ($r instanceof RelationDefinition) {
                $relations[] = $r;

                continue;
            }
            if (!\is_array($r)) {
                throw new \InvalidArgumentException('Each relation entry must be a RelationDefinition or array');
            }
            /** @var array<string, mixed> $r */
            $relations[] = $this->hydrateRelation($r);
        }

        return $relations;
    }

    /**
     * @param array<string, mixed> $r
     */
    private function hydrateRelation(array $r): RelationDefinition
    {
        $name = $r['name'] ?? null;
        if (!\is_string($name) || $name === '') {
            throw new \InvalidArgumentException('relation.name required');
        }

        $type = $r['type'] ?? null;
        $relationType = $type instanceof RelationType ? $type : RelationType::from(\is_string($type) ? $type : '');

        $target = $r['target'] ?? null;
        if (!\is_string($target) || $target === '') {
            throw new \InvalidArgumentException('relation.target required');
        }

        return new RelationDefinition(
            name: $name,
            type: $relationType,
            target: $target,
            label: \is_string($r['label'] ?? null) ? $r['label'] : null,
            description: \is_string($r['description'] ?? null) ? $r['description'] : null,
            foreignKey: \is_string($r['foreignKey'] ?? null) ? $r['foreignKey'] : null,
            relatedKey: \is_string($r['relatedKey'] ?? null) ? $r['relatedKey'] : null,
            pivotTable: \is_string($r['pivotTable'] ?? null) ? $r['pivotTable'] : null,
            meta: $this->toStringKeyedArray($r['meta'] ?? []),
        );
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<ActionDefinition>
     */
    private function hydrateActions(array $item): array
    {
        $raw = $item['actions'] ?? [];
        if (!\is_array($raw)) {
            return [];
        }

        $actions = [];

        foreach ($raw as $a) {
            if ($a instanceof ActionDefinition) {
                $actions[] = $a;

                continue;
            }
            if (!\is_array($a)) {
                throw new \InvalidArgumentException('Each action entry must be an ActionDefinition or array');
            }
            /** @var array<string, mixed> $a */
            $actions[] = $this->hydrateAction($a);
        }

        return $actions;
    }

    /**
     * @param array<string, mixed> $a
     */
    private function hydrateAction(array $a): ActionDefinition
    {
        $name = $a['name'] ?? null;
        if (!\is_string($name) || $name === '') {
            throw new \InvalidArgumentException('action.name required');
        }

        return new ActionDefinition(
            name: $name,
            description: \is_string($a['description'] ?? null) ? $a['description'] : null,
            meta: $this->toStringKeyedArray($a['meta'] ?? []),
            handler: \is_string($a['handler'] ?? null) ? $a['handler'] : null,
        );
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<ComputedFieldDefinition>
     */
    private function hydrateComputedFields(array $item): array
    {
        $raw = $item['computedFields'] ?? [];
        if (!\is_array($raw)) {
            return [];
        }

        $computed = [];

        foreach ($raw as $c) {
            if ($c instanceof ComputedFieldDefinition) {
                $computed[] = $c;

                continue;
            }
            if (!\is_array($c)) {
                throw new \InvalidArgumentException('Each computedField entry must be a ComputedFieldDefinition or array');
            }
            /** @var array<string, mixed> $c */
            $computed[] = $this->hydrateComputedField($c);
        }

        return $computed;
    }

    /**
     * @param array<string, mixed> $c
     */
    private function hydrateComputedField(array $c): ComputedFieldDefinition
    {
        $name = $c['name'] ?? null;
        if (!\is_string($name) || $name === '') {
            throw new \InvalidArgumentException('computedField.name required');
        }

        $type = $c['type'] ?? null;
        $fieldType = $type instanceof FieldType ? $type : FieldType::from(\is_string($type) ? $type : '');

        return new ComputedFieldDefinition(
            name: $name,
            type: $fieldType,
            via: \is_string($c['via'] ?? null) ? $c['via'] : null,
            resolver: \is_string($c['resolver'] ?? null) ? $c['resolver'] : null,
            label: \is_string($c['label'] ?? null) ? $c['label'] : null,
            description: \is_string($c['description'] ?? null) ? $c['description'] : null,
            meta: $this->toStringKeyedArray($c['meta'] ?? []),
        );
    }

    /**
     * @return list<string>
     */
    private function toStringList(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        $result = [];

        foreach ($value as $item) {
            $result[] = \is_string($item) ? $item : (string) (\is_scalar($item) ? $item : '');
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function toStringKeyedArray(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        /** @var array<string, mixed> */
        return $value;
    }
}

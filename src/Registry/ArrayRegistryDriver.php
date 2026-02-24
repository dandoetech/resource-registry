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
            $out[] = $this->hydrate((string) $key, $item);
        }

        return $out;
    }

    public function find(string $key): ?ResourceDefinitionInterface
    {
        /** @var array<string, mixed>|null $item */
        $item = $this->config[$key] ?? null;

        return $item === null ? null : $this->hydrate($key, $item);
    }

    /** @param array<string, mixed> $item */
    private function hydrate(string $key, array $item): ResourceDefinition
    {
        return new ResourceDefinition(
            key: $key,
            label: (string) ($item['label'] ?? \ucwords(\str_replace('_', ' ', $key))),
            fields: $this->hydrateFields($item),
            relations: $this->hydrateRelations($item),
            actions: $this->hydrateActions($item),
            description: isset($item['description']) ? (string) $item['description'] : null,
            version: (int) ($item['version'] ?? 1),
            timestamps: (bool) ($item['timestamps'] ?? false),
            softDeletes: (bool) ($item['softDeletes'] ?? false),
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
        $fields = [];
        /** @var mixed $f */
        foreach ($item['fields'] ?? [] as $f) {
            if ($f instanceof FieldDefinition) {
                $fields[] = $f;

                continue;
            }
            if (!\is_array($f)) {
                throw new \InvalidArgumentException('Each field entry must be a FieldDefinition or array');
            }
            $fields[] = new FieldDefinition(
                name: (string) ($f['name'] ?? throw new \InvalidArgumentException('field.name required')),
                type: $f['type'] instanceof FieldType ? $f['type'] : FieldType::from((string) $f['type']),
                nullable: (bool) ($f['nullable'] ?? true),
                rules: $this->toStringList($f['rules'] ?? []),
                label: isset($f['label']) ? (string) $f['label'] : null,
                description: isset($f['description']) ? (string) $f['description'] : null,
                unique: (bool) ($f['unique'] ?? false),
                indexed: (bool) ($f['indexed'] ?? false),
                default: $f['default'] ?? null,
                comment: isset($f['comment']) ? (string) $f['comment'] : null,
                meta: $this->toStringKeyedArray($f['meta'] ?? []),
            );
        }

        return $fields;
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<RelationDefinition>
     */
    private function hydrateRelations(array $item): array
    {
        $relations = [];
        /** @var mixed $r */
        foreach ($item['relations'] ?? [] as $r) {
            if ($r instanceof RelationDefinition) {
                $relations[] = $r;

                continue;
            }
            if (!\is_array($r)) {
                throw new \InvalidArgumentException('Each relation entry must be a RelationDefinition or array');
            }
            $relations[] = new RelationDefinition(
                name: (string) ($r['name'] ?? throw new \InvalidArgumentException('relation.name required')),
                type: $r['type'] instanceof RelationType ? $r['type'] : RelationType::from((string) $r['type']),
                target: (string) ($r['target'] ?? throw new \InvalidArgumentException('relation.target required')),
                label: isset($r['label']) ? (string) $r['label'] : null,
                description: isset($r['description']) ? (string) $r['description'] : null,
                foreignKey: isset($r['foreignKey']) ? (string) $r['foreignKey'] : null,
                relatedKey: isset($r['relatedKey']) ? (string) $r['relatedKey'] : null,
                pivotTable: isset($r['pivotTable']) ? (string) $r['pivotTable'] : null,
                meta: $this->toStringKeyedArray($r['meta'] ?? []),
            );
        }

        return $relations;
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<ActionDefinition>
     */
    private function hydrateActions(array $item): array
    {
        $actions = [];
        /** @var mixed $a */
        foreach ($item['actions'] ?? [] as $a) {
            if ($a instanceof ActionDefinition) {
                $actions[] = $a;

                continue;
            }
            if (!\is_array($a)) {
                throw new \InvalidArgumentException('Each action entry must be an ActionDefinition or array');
            }
            $actions[] = new ActionDefinition(
                name: (string) ($a['name'] ?? throw new \InvalidArgumentException('action.name required')),
                description: isset($a['description']) ? (string) $a['description'] : null,
                meta: $this->toStringKeyedArray($a['meta'] ?? []),
            );
        }

        return $actions;
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return list<ComputedFieldDefinition>
     */
    private function hydrateComputedFields(array $item): array
    {
        $computed = [];
        /** @var mixed $c */
        foreach ($item['computedFields'] ?? [] as $c) {
            if ($c instanceof ComputedFieldDefinition) {
                $computed[] = $c;

                continue;
            }
            if (!\is_array($c)) {
                throw new \InvalidArgumentException('Each computedField entry must be a ComputedFieldDefinition or array');
            }
            $computed[] = new ComputedFieldDefinition(
                name: (string) ($c['name'] ?? throw new \InvalidArgumentException('computedField.name required')),
                type: $c['type'] instanceof FieldType ? $c['type'] : FieldType::from((string) $c['type']),
                via: isset($c['via']) ? (string) $c['via'] : null,
                resolver: isset($c['resolver']) ? (string) $c['resolver'] : null,
                label: isset($c['label']) ? (string) $c['label'] : null,
                description: isset($c['description']) ? (string) $c['description'] : null,
            );
        }

        return $computed;
    }

    /**
     * @param mixed $value
     *
     * @return list<string>
     */
    private function toStringList(mixed $value): array
    {
        if (!\is_array($value)) {
            return [];
        }

        return \array_values(\array_map('strval', $value));
    }

    /**
     * @param mixed $value
     *
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

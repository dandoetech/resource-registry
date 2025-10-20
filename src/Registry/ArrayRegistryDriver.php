<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Registry;

use DanDoeTech\ResourceRegistry\Contracts\RegistryDriverInterface;
use DanDoeTech\ResourceRegistry\Definition\{ActionDefinition,
    FieldDefinition,
    FieldType,
    RelationDefinition,
    RelationType,
    ResourceDefinition};

final class ArrayRegistryDriver implements RegistryDriverInterface
{
    /** @param array<string, array> $config */
    public function __construct(private array $config)
    {
    }

    /** @return list<ResourceDefinition> */
    public function all(): array
    {
        $out = [];
        foreach ($this->config as $key => $item) {
            $out[] = $this->hydrate((string)$key, $item);
        }

        return $out;
    }

    /** @param array<string, mixed> $item */
    private function hydrate(string $key, array $item): ResourceDefinition
    {
        $fields = [];
        foreach ($item['fields'] ?? [] as $f) {
            if ($f instanceof FieldDefinition) {
                $fields[] = $f;
                continue;
            }
            $fields[] = new FieldDefinition(
                name: (string)($f['name'] ?? throw new \InvalidArgumentException('field.name required')),
                type: $f['type'] instanceof FieldType ? $f['type'] : FieldType::from((string)$f['type']),
                nullable: (bool)($f['nullable'] ?? true),
                rules: \array_values($f['rules'] ?? []),
                label: $f['label'] ?? null,
                description: $f['description'] ?? null,
            );
        }

        $relations = [];
        foreach ($item['relations'] ?? [] as $r) {
            if ($r instanceof RelationDefinition) {
                $relations[] = $r;
                continue;
            }
            $relations[] = new RelationDefinition(
                name: (string)($r['name'] ?? throw new \InvalidArgumentException('relation.name required')),
                type: $r['type'] instanceof RelationType ? $r['type'] : RelationType::from((string)$r['type']),
                target: (string)($r['target'] ?? throw new \InvalidArgumentException('relation.target required')),
                label: $r['label'] ?? null,
                description: $r['description'] ?? null,
            );
        }

        $actions = [];
        foreach ($item['actions'] ?? [] as $a) {
            if ($a instanceof ActionDefinition) {
                $actions[] = $a;
                continue;
            }
            $actions[] = new ActionDefinition(
                name: (string)($a['name'] ?? throw new \InvalidArgumentException('action.name required')),
                description: $a['description'] ?? null,
            );
        }

        return new ResourceDefinition(
            key: $key,
            label: (string)($item['label'] ?? \ucfirst($key)),
            fields: $fields,
            relations: $relations,
            actions: $actions,
            description: $item['description'] ?? null,
        );
    }

    public function find(string $key): ?ResourceDefinition
    {
        $item = $this->config[$key] ?? null;

        return $item === null ? null : $this->hydrate($key, $item);
    }
}

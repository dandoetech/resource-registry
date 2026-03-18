<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

use DanDoeTech\ResourceRegistry\Definition\QueryProfile;

interface ResourceDefinitionInterface
{
    public function getKey(): string;

    public function getVersion(): int;

    public function getLabel(): string;

    /** @return list<FieldDefinitionInterface> */
    public function getFields(): array;

    public function getField(string $name): ?FieldDefinitionInterface;

    /** @return list<RelationDefinitionInterface> */
    public function getRelations(): array;

    /** @return list<ActionDefinitionInterface> */
    public function getActions(): array;

    /** @return list<ComputedFieldDefinitionInterface> */
    public function getComputedFields(): array;

    public function isTimestamped(): bool;

    public function usesSoftDeletes(): bool;

    /** @return list<string> */
    public function getFilterable(): array;

    /** @return list<string> */
    public function getSortable(): array;

    /** @return list<string> */
    public function getSearchable(): array;

    public function getDescription(): ?string;

    /** @return array<string, mixed> */
    public function getMeta(): array;

    /** @return array<string, QueryProfile> */
    public function getQueryProfiles(): array;

    public function getRouteSegment(): ?string;
}

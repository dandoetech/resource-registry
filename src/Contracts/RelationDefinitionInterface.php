<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

use DanDoeTech\ResourceRegistry\Definition\RelationType;

interface RelationDefinitionInterface
{
    public function getName(): string;

    public function getType(): RelationType;

    public function getTarget(): string;

    public function getLabel(): ?string;

    public function getDescription(): ?string;

    public function getForeignKey(): ?string;

    public function getRelatedKey(): ?string;

    public function getPivotTable(): ?string;

    /** @return array<string, mixed> */
    public function getMeta(): array;
}

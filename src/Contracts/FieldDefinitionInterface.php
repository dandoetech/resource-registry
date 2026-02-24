<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

use DanDoeTech\ResourceRegistry\Definition\FieldType;

interface FieldDefinitionInterface
{
    public function getName(): string;

    public function getType(): FieldType;

    public function isNullable(): bool;

    /** @return list<string> */
    public function getRules(): array;

    public function getLabel(): ?string;

    public function getDescription(): ?string;

    public function isUnique(): bool;

    public function isIndexed(): bool;

    public function getDefault(): mixed;

    public function getComment(): ?string;

    /** @return array<string, mixed> */
    public function getMeta(): array;
}

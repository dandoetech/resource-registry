<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Contracts;

use DanDoeTech\ResourceRegistry\Definition\FieldType;

interface ComputedFieldDefinitionInterface
{
    public function getName(): string;

    public function getType(): FieldType;

    /** e.g. 'category.name', 'count:orders', or null if custom resolver */
    public function getVia(): ?string;

    /** FQCN of a resolver class, or null if via is sufficient */
    public function getResolver(): ?string;

    public function getLabel(): ?string;

    public function getDescription(): ?string;

    /** @return array<string, mixed> */
    public function getMeta(): array;
}

<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

use DanDoeTech\ResourceRegistry\Contracts\FieldDefinitionInterface;

final class FieldDefinition implements FieldDefinitionInterface
{
    /**
     * @param list<string>         $rules
     * @param array<string, mixed> $meta
     */
    public function __construct(
        public string    $name,
        public FieldType $type,
        public bool      $nullable = true,
        public array     $rules = [],
        public ?string   $label = null,
        public ?string   $description = null,
        public bool      $unique = false,
        public bool      $indexed = false,
        public mixed     $default = null,
        public ?string   $comment = null,
        public array     $meta = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): FieldType
    {
        return $this->type;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /** @return list<string> */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isUnique(): bool
    {
        return $this->unique;
    }

    public function isIndexed(): bool
    {
        return $this->indexed;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    /** @return array<string, mixed> */
    public function getMeta(): array
    {
        return $this->meta;
    }
}

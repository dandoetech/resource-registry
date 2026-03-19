<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Definition;

use DanDoeTech\ResourceRegistry\Contracts\FieldDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\FieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FieldDefinitionTest extends TestCase
{
    #[Test]
    public function implementsInterface(): void
    {
        $field = new FieldDefinition(name: 'title', type: FieldType::String);

        self::assertInstanceOf(FieldDefinitionInterface::class, $field);
    }

    #[Test]
    public function allGettersReturnConstructorValues(): void
    {
        $field = new FieldDefinition(
            name: 'email',
            type: FieldType::Email,
            nullable: false,
            rules: ['required', 'email'],
            label: 'E-Mail',
            description: 'User email address',
            unique: true,
            indexed: true,
            default: 'test@example.com',
            comment: 'Must be unique',
            meta: ['weight' => 10],
        );

        self::assertSame('email', $field->getName());
        self::assertSame(FieldType::Email, $field->getType());
        self::assertFalse($field->isNullable());
        self::assertSame(['required', 'email'], $field->getRules());
        self::assertSame('E-Mail', $field->getLabel());
        self::assertSame('User email address', $field->getDescription());
        self::assertTrue($field->isUnique());
        self::assertTrue($field->isIndexed());
        self::assertSame('test@example.com', $field->getDefault());
        self::assertSame('Must be unique', $field->getComment());
        self::assertSame(['weight' => 10], $field->getMeta());
    }

    #[Test]
    public function defaultValuesAreCorrect(): void
    {
        $field = new FieldDefinition(name: 'status', type: FieldType::String);

        self::assertTrue($field->isNullable());
        self::assertSame([], $field->getRules());
        self::assertNull($field->getLabel());
        self::assertNull($field->getDescription());
        self::assertFalse($field->isUnique());
        self::assertFalse($field->isIndexed());
        self::assertNull($field->getDefault());
        self::assertNull($field->getComment());
        self::assertSame([], $field->getMeta());
    }

    #[Test]
    public function defaultCanBeNonNullValue(): void
    {
        $field = new FieldDefinition(
            name: 'status',
            type: FieldType::String,
            default: 'draft',
        );

        self::assertSame('draft', $field->getDefault());
    }

    #[Test]
    public function defaultCanBeZeroOrFalse(): void
    {
        $intField = new FieldDefinition(
            name: 'count',
            type: FieldType::Integer,
            default: 0,
        );
        self::assertSame(0, $intField->getDefault());

        $boolField = new FieldDefinition(
            name: 'active',
            type: FieldType::Boolean,
            default: false,
        );
        self::assertFalse($boolField->getDefault());
    }

    #[Test]
    public function metaCanHoldNestedArrays(): void
    {
        $field = new FieldDefinition(
            name: 'data',
            type: FieldType::Json,
            meta: ['nested' => ['key' => 'value']],
        );

        self::assertSame(['nested' => ['key' => 'value']], $field->getMeta());
    }
}

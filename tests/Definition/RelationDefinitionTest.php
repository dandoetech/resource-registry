<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Definition;

use DanDoeTech\ResourceRegistry\Contracts\RelationDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\RelationDefinition;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RelationDefinitionTest extends TestCase
{
    #[Test]
    public function implementsInterface(): void
    {
        $relation = new RelationDefinition(
            name: 'category',
            type: RelationType::BelongsTo,
            target: 'category',
        );

        self::assertInstanceOf(RelationDefinitionInterface::class, $relation);
    }

    #[Test]
    public function allGettersReturnConstructorValues(): void
    {
        $relation = new RelationDefinition(
            name: 'roles',
            type: RelationType::BelongsToMany,
            target: 'role',
            label: 'User Roles',
            description: 'Roles assigned to the user',
            foreignKey: 'user_id',
            relatedKey: 'role_id',
            pivotTable: 'role_user',
            meta: ['cascade' => true],
        );

        self::assertSame('roles', $relation->getName());
        self::assertSame(RelationType::BelongsToMany, $relation->getType());
        self::assertSame('role', $relation->getTarget());
        self::assertSame('User Roles', $relation->getLabel());
        self::assertSame('Roles assigned to the user', $relation->getDescription());
        self::assertSame('user_id', $relation->getForeignKey());
        self::assertSame('role_id', $relation->getRelatedKey());
        self::assertSame('role_user', $relation->getPivotTable());
        self::assertSame(['cascade' => true], $relation->getMeta());
    }

    #[Test]
    public function defaultValuesAreCorrect(): void
    {
        $relation = new RelationDefinition(
            name: 'author',
            type: RelationType::BelongsTo,
            target: 'user',
        );

        self::assertNull($relation->getLabel());
        self::assertNull($relation->getDescription());
        self::assertNull($relation->getForeignKey());
        self::assertNull($relation->getRelatedKey());
        self::assertNull($relation->getPivotTable());
        self::assertSame([], $relation->getMeta());
    }

    #[Test]
    public function allRelationTypesAreSupported(): void
    {
        $types = [
            RelationType::BelongsTo,
            RelationType::HasMany,
            RelationType::HasOne,
            RelationType::BelongsToMany,
            RelationType::MorphTo,
            RelationType::MorphMany,
        ];

        foreach ($types as $type) {
            $relation = new RelationDefinition(
                name: 'test',
                type: $type,
                target: 'test',
            );

            self::assertSame($type, $relation->getType());
        }
    }
}

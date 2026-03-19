<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Registry;

use DanDoeTech\ResourceRegistry\Definition\ActionDefinition;
use DanDoeTech\ResourceRegistry\Definition\ComputedFieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldDefinition;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationDefinition;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Registry\ArrayRegistryDriver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ArrayRegistryDriverTest extends TestCase
{
    #[Test]
    public function findReturnsNullForMissingKey(): void
    {
        $driver = new ArrayRegistryDriver([]);

        self::assertNull($driver->find('missing'));
    }

    #[Test]
    public function allReturnsEmptyForEmptyConfig(): void
    {
        $driver = new ArrayRegistryDriver([]);

        self::assertSame([], $driver->all());
    }

    #[Test]
    public function emptyKeyThrowsException(): void
    {
        $driver = new ArrayRegistryDriver(['' => ['label' => 'Bad']]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource key must not be empty');

        $driver->all();
    }

    #[Test]
    public function hydratesLabelFromKey(): void
    {
        $driver = new ArrayRegistryDriver([
            'order_item' => [],
        ]);

        $resource = $driver->find('order_item');
        self::assertNotNull($resource);
        self::assertSame('Order Item', $resource->getLabel());
    }

    #[Test]
    public function hydratesVersionDefaultsToOne(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertSame(1, $resource->getVersion());
    }

    #[Test]
    public function acceptsPrebuiltValueObjects(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'label'  => 'Product',
                'fields' => [
                    new FieldDefinition(name: 'name', type: FieldType::String),
                ],
                'relations' => [
                    new RelationDefinition(name: 'category', type: RelationType::BelongsTo, target: 'category'),
                ],
                'actions' => [
                    new ActionDefinition(name: 'create'),
                ],
                'computedFields' => [
                    new ComputedFieldDefinition(name: 'count', type: FieldType::Integer, via: 'count:orders'),
                ],
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertCount(1, $resource->getFields());
        self::assertCount(1, $resource->getRelations());
        self::assertCount(1, $resource->getActions());
        self::assertCount(1, $resource->getComputedFields());
    }

    #[Test]
    public function invalidFieldEntryThrows(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'fields' => ['not-an-array'],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each field entry must be a FieldDefinition or array');

        $driver->find('product');
    }

    #[Test]
    public function fieldWithoutNameThrows(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'fields' => [
                    ['type' => 'string'],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('field.name required');

        $driver->find('product');
    }

    #[Test]
    public function invalidRelationEntryThrows(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'relations' => ['not-an-array'],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each relation entry must be a RelationDefinition or array');

        $driver->find('product');
    }

    #[Test]
    public function invalidActionEntryThrows(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'actions' => ['not-an-array'],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each action entry must be an ActionDefinition or array');

        $driver->find('product');
    }

    #[Test]
    public function invalidComputedFieldEntryThrows(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'computedFields' => ['not-an-array'],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Each computedField entry must be a ComputedFieldDefinition or array');

        $driver->find('product');
    }

    #[Test]
    public function hydratesTimestampsAndSoftDeletes(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'timestamps'  => true,
                'softDeletes' => true,
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertTrue($resource->isTimestamped());
        self::assertTrue($resource->usesSoftDeletes());
    }

    #[Test]
    public function hydratesFilterableSortableSearchable(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'filterable' => ['name', 'price'],
                'sortable'   => ['name'],
                'searchable' => ['name', 'description'],
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertSame(['name', 'price'], $resource->getFilterable());
        self::assertSame(['name'], $resource->getSortable());
        self::assertSame(['name', 'description'], $resource->getSearchable());
    }
}

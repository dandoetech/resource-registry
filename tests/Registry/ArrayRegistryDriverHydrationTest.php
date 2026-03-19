<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Registry;

use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Registry\ArrayRegistryDriver;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests the array-based hydration paths of ArrayRegistryDriver (not pre-built VOs).
 */
final class ArrayRegistryDriverHydrationTest extends TestCase
{
    #[Test]
    public function hydrates_field_from_array_with_type_string(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'label'  => 'Product',
                'fields' => [
                    ['name' => 'title', 'type' => 'string', 'nullable' => false],
                ],
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertCount(1, $resource->getFields());

        $field = $resource->getField('title');
        self::assertNotNull($field);
        self::assertSame(FieldType::String, $field->getType());
        self::assertFalse($field->isNullable());
    }

    #[Test]
    public function hydrates_field_with_all_optional_properties(): void
    {
        $driver = new ArrayRegistryDriver([
            'setting' => [
                'label'  => 'Setting',
                'fields' => [
                    [
                        'name'        => 'key',
                        'type'        => 'string',
                        'nullable'    => false,
                        'rules'       => ['required', 'max:100'],
                        'label'       => 'Config Key',
                        'description' => 'The setting key',
                        'unique'      => true,
                        'indexed'     => true,
                        'default'     => 'none',
                        'comment'     => 'Must be unique',
                        'meta'        => ['searchWeight' => 10],
                    ],
                ],
            ],
        ]);

        $field = $driver->find('setting')?->getField('key');
        self::assertNotNull($field);
        self::assertSame('Config Key', $field->getLabel());
        self::assertSame('The setting key', $field->getDescription());
        self::assertSame(['required', 'max:100'], $field->getRules());
        self::assertTrue($field->isUnique());
        self::assertTrue($field->isIndexed());
        self::assertSame('none', $field->getDefault());
        self::assertSame('Must be unique', $field->getComment());
        self::assertSame(['searchWeight' => 10], $field->getMeta());
    }

    #[Test]
    public function hydrates_relation_from_array(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'label'     => 'Product',
                'relations' => [
                    [
                        'name'       => 'category',
                        'type'       => 'belongs_to',
                        'target'     => 'category',
                        'foreignKey' => 'category_id',
                    ],
                ],
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertCount(1, $resource->getRelations());

        $rel = $resource->getRelations()[0];
        self::assertSame('category', $rel->getName());
        self::assertSame(RelationType::BelongsTo, $rel->getType());
        self::assertSame('category', $rel->getTarget());
        self::assertSame('category_id', $rel->getForeignKey());
    }

    #[Test]
    public function hydrates_action_from_array(): void
    {
        $driver = new ArrayRegistryDriver([
            'order' => [
                'label'   => 'Order',
                'actions' => [
                    [
                        'name'        => 'cancel',
                        'description' => 'Cancel this order',
                        'handler'     => 'App\\Actions\\CancelHandler',
                        'meta'        => ['confirm' => true],
                    ],
                ],
            ],
        ]);

        $resource = $driver->find('order');
        self::assertNotNull($resource);

        $action = $resource->getActions()[0];
        self::assertSame('cancel', $action->getName());
        self::assertSame('Cancel this order', $action->getDescription());
        self::assertSame('App\\Actions\\CancelHandler', $action->getHandler());
        self::assertSame(['confirm' => true], $action->getMeta());
    }

    #[Test]
    public function hydrates_computed_field_from_array(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'label'          => 'Product',
                'computedFields' => [
                    [
                        'name'        => 'category_name',
                        'type'        => 'string',
                        'via'         => 'category.name',
                        'label'       => 'Category',
                        'description' => 'Name of the category',
                    ],
                ],
            ],
        ]);

        $computed = $driver->find('product')?->getComputedFields()[0];
        self::assertNotNull($computed);
        self::assertSame('category_name', $computed->getName());
        self::assertSame(FieldType::String, $computed->getType());
        self::assertSame('category.name', $computed->getVia());
        self::assertNull($computed->getResolver());
        self::assertSame('Category', $computed->getLabel());
        self::assertSame('Name of the category', $computed->getDescription());
    }

    #[Test]
    public function hydrates_description_and_version(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'label'       => 'Product',
                'description' => 'A catalog product',
                'version'     => 3,
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertSame('A catalog product', $resource->getDescription());
        self::assertSame(3, $resource->getVersion());
    }

    #[Test]
    public function hydrates_meta(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'label' => 'Product',
                'meta'  => ['icon' => 'box', 'group' => 'catalog'],
            ],
        ]);

        $resource = $driver->find('product');
        self::assertNotNull($resource);
        self::assertSame(['icon' => 'box', 'group' => 'catalog'], $resource->getMeta());
    }

    #[Test]
    public function relation_without_name_throws(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'relations' => [
                    ['type' => 'belongsTo', 'target' => 'category'],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('relation.name required');

        $driver->find('product');
    }

    #[Test]
    public function relation_without_target_throws(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'relations' => [
                    ['name' => 'category', 'type' => 'belongs_to'],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('relation.target required');

        $driver->find('product');
    }

    #[Test]
    public function action_without_name_throws(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'actions' => [
                    ['description' => 'missing name'],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('action.name required');

        $driver->find('product');
    }

    #[Test]
    public function computed_field_without_name_throws(): void
    {
        $driver = new ArrayRegistryDriver([
            'product' => [
                'computedFields' => [
                    ['type' => 'string', 'via' => 'category.name'],
                ],
            ],
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('computedField.name required');

        $driver->find('product');
    }
}

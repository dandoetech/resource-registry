<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Builder;

use DanDoeTech\ResourceRegistry\Builder\ResourceBuilder;
use DanDoeTech\ResourceRegistry\Definition\FieldType;
use DanDoeTech\ResourceRegistry\Definition\RelationType;
use DanDoeTech\ResourceRegistry\Definition\ResourceDefinition;
use PHPUnit\Framework\TestCase;

final class ResourceBuilderTest extends TestCase
{
    public function testBuildMinimalResource(): void
    {
        $resource = (new ResourceBuilder())
            ->key('product')
            ->build();

        self::assertInstanceOf(ResourceDefinition::class, $resource);
        self::assertSame('product', $resource->getKey());
        self::assertSame('Product', $resource->getLabel());
        self::assertSame(1, $resource->getVersion());
        self::assertFalse($resource->isTimestamped());
        self::assertFalse($resource->usesSoftDeletes());
        self::assertSame([], $resource->getFields());
        self::assertSame([], $resource->getRelations());
        self::assertSame([], $resource->getActions());
        self::assertSame([], $resource->getComputedFields());
        self::assertSame([], $resource->getFilterable());
        self::assertSame([], $resource->getSortable());
        self::assertSame([], $resource->getSearchable());
        self::assertNull($resource->getDescription());
        self::assertSame([], $resource->getMeta());
    }

    public function testBuildFullProductResource(): void
    {
        $resource = (new ResourceBuilder())
            ->key('product')
            ->version(2)
            ->label('Product')
            ->description('A product in the catalog')
            ->timestamps()
            ->softDeletes()
            ->field('name', FieldType::String, nullable: false, rules: ['required', 'max:120'])
            ->field('price', FieldType::Float, nullable: false, rules: ['min:0'])
            ->field('description', FieldType::String, label: 'Beschreibung')
            ->field('category_id', FieldType::Integer, nullable: false)
            ->belongsTo('category', foreignKey: 'category_id')
            ->hasMany('reviews')
            ->hasMany('orders')
            ->computed('category_name', FieldType::String, via: 'category.name')
            ->computed('orders_count', FieldType::Integer, via: 'count:orders')
            ->filterable(['name', 'price', 'category_id', 'category_name'])
            ->sortable(['name', 'price', 'created_at', 'orders_count'])
            ->searchable(['name', 'description'])
            ->action('create')
            ->action('update')
            ->action('delete')
            ->meta(['icon' => 'box'])
            ->build();

        self::assertSame('product', $resource->getKey());
        self::assertSame(2, $resource->getVersion());
        self::assertSame('Product', $resource->getLabel());
        self::assertSame('A product in the catalog', $resource->getDescription());
        self::assertTrue($resource->isTimestamped());
        self::assertTrue($resource->usesSoftDeletes());

        // Fields
        self::assertCount(4, $resource->getFields());
        $nameField = $resource->getField('name');
        self::assertNotNull($nameField);
        self::assertSame('name', $nameField->getName());
        self::assertSame(FieldType::String, $nameField->getType());
        self::assertFalse($nameField->isNullable());
        self::assertSame(['required', 'max:120'], $nameField->getRules());

        $descField = $resource->getField('description');
        self::assertNotNull($descField);
        self::assertSame('Beschreibung', $descField->getLabel());

        self::assertNull($resource->getField('nonexistent'));

        // Relations
        self::assertCount(3, $resource->getRelations());
        $category = $resource->getRelations()[0];
        self::assertSame('category', $category->getName());
        self::assertSame(RelationType::BelongsTo, $category->getType());
        self::assertSame('category', $category->getTarget());
        self::assertSame('category_id', $category->getForeignKey());

        $reviews = $resource->getRelations()[1];
        self::assertSame('reviews', $reviews->getName());
        self::assertSame(RelationType::HasMany, $reviews->getType());
        self::assertSame('reviews', $reviews->getTarget());

        // Computed fields
        self::assertCount(2, $resource->getComputedFields());
        $categoryName = $resource->getComputedFields()[0];
        self::assertSame('category_name', $categoryName->getName());
        self::assertSame(FieldType::String, $categoryName->getType());
        self::assertSame('category.name', $categoryName->getVia());
        self::assertNull($categoryName->getResolver());
        self::assertSame([], $categoryName->getMeta());

        $ordersCount = $resource->getComputedFields()[1];
        self::assertSame('orders_count', $ordersCount->getName());
        self::assertSame('count:orders', $ordersCount->getVia());

        // Filterable, sortable, searchable
        self::assertSame(['name', 'price', 'category_id', 'category_name'], $resource->getFilterable());
        self::assertSame(['name', 'price', 'created_at', 'orders_count'], $resource->getSortable());
        self::assertSame(['name', 'description'], $resource->getSearchable());

        // Actions
        self::assertCount(3, $resource->getActions());
        self::assertSame('create', $resource->getActions()[0]->getName());
        self::assertSame('update', $resource->getActions()[1]->getName());
        self::assertSame('delete', $resource->getActions()[2]->getName());

        // Meta
        self::assertSame(['icon' => 'box'], $resource->getMeta());
    }

    public function testFieldWithAllProperties(): void
    {
        $resource = (new ResourceBuilder())
            ->key('user')
            ->field(
                'email',
                FieldType::String,
                nullable: false,
                rules: ['required', 'email'],
                label: 'E-Mail',
                description: 'User email address',
                unique: true,
                indexed: true,
                default: null,
                comment: 'Must be unique across tenants',
                meta: ['searchWeight' => 10],
            )
            ->build();

        $email = $resource->getField('email');
        self::assertNotNull($email);
        self::assertSame('email', $email->getName());
        self::assertSame(FieldType::String, $email->getType());
        self::assertFalse($email->isNullable());
        self::assertSame(['required', 'email'], $email->getRules());
        self::assertSame('E-Mail', $email->getLabel());
        self::assertSame('User email address', $email->getDescription());
        self::assertTrue($email->isUnique());
        self::assertTrue($email->isIndexed());
        self::assertNull($email->getDefault());
        self::assertSame('Must be unique across tenants', $email->getComment());
        self::assertSame(['searchWeight' => 10], $email->getMeta());
    }

    public function testFieldDefaults(): void
    {
        $resource = (new ResourceBuilder())
            ->key('item')
            ->field('status', FieldType::String, default: 'draft')
            ->build();

        $status = $resource->getField('status');
        self::assertNotNull($status);
        self::assertTrue($status->isNullable());
        self::assertSame([], $status->getRules());
        self::assertNull($status->getLabel());
        self::assertNull($status->getDescription());
        self::assertFalse($status->isUnique());
        self::assertFalse($status->isIndexed());
        self::assertSame('draft', $status->getDefault());
        self::assertNull($status->getComment());
        self::assertSame([], $status->getMeta());
    }

    public function testBelongsToDefaultsTargetToName(): void
    {
        $resource = (new ResourceBuilder())
            ->key('post')
            ->belongsTo('author')
            ->build();

        self::assertCount(1, $resource->getRelations());
        $rel = $resource->getRelations()[0];
        self::assertSame('author', $rel->getName());
        self::assertSame(RelationType::BelongsTo, $rel->getType());
        self::assertSame('author', $rel->getTarget());
        self::assertNull($rel->getForeignKey());
    }

    public function testBelongsToWithExplicitTarget(): void
    {
        $resource = (new ResourceBuilder())
            ->key('post')
            ->belongsTo('writer', target: 'user', foreignKey: 'writer_id')
            ->build();

        $rel = $resource->getRelations()[0];
        self::assertSame('writer', $rel->getName());
        self::assertSame('user', $rel->getTarget());
        self::assertSame('writer_id', $rel->getForeignKey());
    }

    public function testHasOneRelation(): void
    {
        $resource = (new ResourceBuilder())
            ->key('user')
            ->hasOne('profile', foreignKey: 'user_id')
            ->build();

        $rel = $resource->getRelations()[0];
        self::assertSame('profile', $rel->getName());
        self::assertSame(RelationType::HasOne, $rel->getType());
        self::assertSame('profile', $rel->getTarget());
        self::assertSame('user_id', $rel->getForeignKey());
    }

    public function testBelongsToManyWithPivot(): void
    {
        $resource = (new ResourceBuilder())
            ->key('user')
            ->belongsToMany('roles', pivotTable: 'role_user', foreignKey: 'user_id', relatedKey: 'role_id')
            ->build();

        $rel = $resource->getRelations()[0];
        self::assertSame('roles', $rel->getName());
        self::assertSame(RelationType::BelongsToMany, $rel->getType());
        self::assertSame('roles', $rel->getTarget());
        self::assertSame('role_user', $rel->getPivotTable());
        self::assertSame('user_id', $rel->getForeignKey());
        self::assertSame('role_id', $rel->getRelatedKey());
    }

    public function testMorphToRelation(): void
    {
        $resource = (new ResourceBuilder())
            ->key('comment')
            ->morphTo('commentable')
            ->build();

        $rel = $resource->getRelations()[0];
        self::assertSame('commentable', $rel->getName());
        self::assertSame(RelationType::MorphTo, $rel->getType());
        self::assertSame('commentable', $rel->getTarget());
    }

    public function testMorphManyRelation(): void
    {
        $resource = (new ResourceBuilder())
            ->key('post')
            ->morphMany('comments', target: 'comment', foreignKey: 'commentable_id')
            ->build();

        $rel = $resource->getRelations()[0];
        self::assertSame('comments', $rel->getName());
        self::assertSame(RelationType::MorphMany, $rel->getType());
        self::assertSame('comment', $rel->getTarget());
        self::assertSame('commentable_id', $rel->getForeignKey());
    }

    public function testRelationMeta(): void
    {
        $resource = (new ResourceBuilder())
            ->key('order')
            ->hasMany('items', label: 'Order Items', description: 'Line items', meta: ['cascade' => true])
            ->build();

        $rel = $resource->getRelations()[0];
        self::assertSame('Order Items', $rel->getLabel());
        self::assertSame('Line items', $rel->getDescription());
        self::assertSame(['cascade' => true], $rel->getMeta());
    }

    public function testComputedFieldWithResolver(): void
    {
        $resource = (new ResourceBuilder())
            ->key('product')
            ->computed(
                'revenue',
                FieldType::Float,
                resolver: 'App\\Resolvers\\ProductRevenue',
                label: 'Total Revenue',
                description: 'Sum of all order line totals',
                meta: ['currency' => 'EUR'],
            )
            ->build();

        self::assertCount(1, $resource->getComputedFields());
        $computed = $resource->getComputedFields()[0];
        self::assertSame('revenue', $computed->getName());
        self::assertSame(FieldType::Float, $computed->getType());
        self::assertNull($computed->getVia());
        self::assertSame('App\\Resolvers\\ProductRevenue', $computed->getResolver());
        self::assertSame('Total Revenue', $computed->getLabel());
        self::assertSame('Sum of all order line totals', $computed->getDescription());
        self::assertSame(['currency' => 'EUR'], $computed->getMeta());
    }

    public function testActionWithDescriptionAndMeta(): void
    {
        $resource = (new ResourceBuilder())
            ->key('order')
            ->action('cancel', description: 'Cancel this order', meta: ['confirm' => true])
            ->build();

        self::assertCount(1, $resource->getActions());
        $action = $resource->getActions()[0];
        self::assertSame('cancel', $action->getName());
        self::assertSame('Cancel this order', $action->getDescription());
        self::assertSame(['confirm' => true], $action->getMeta());
    }

    public function testLabelDefaultsToUcwordsKey(): void
    {
        $resource = (new ResourceBuilder())
            ->key('order_item')
            ->build();

        self::assertSame('Order Item', $resource->getLabel());
    }

    public function testBuildThrowsWithoutKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource key is required');

        (new ResourceBuilder())->build();
    }

    public function testBuildThrowsWithEmptyKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Resource key is required');

        (new ResourceBuilder())->key('')->build();
    }

    public function testDuplicateFieldNameThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Duplicate field name: 'name'");

        (new ResourceBuilder())
            ->key('product')
            ->field('name', FieldType::String)
            ->field('name', FieldType::String);
    }

    public function testDuplicateRelationNameThrows(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Duplicate relation name: 'category'");

        (new ResourceBuilder())
            ->key('product')
            ->belongsTo('category')
            ->hasMany('category');
    }

    public function testVersionDefaultsToOne(): void
    {
        $resource = (new ResourceBuilder())
            ->key('product')
            ->build();

        self::assertSame(1, $resource->getVersion());
    }

    public function testFluentChainingReturnsBuilder(): void
    {
        $builder = new ResourceBuilder();

        self::assertSame($builder, $builder->key('x'));
        self::assertSame($builder, $builder->version(1));
        self::assertSame($builder, $builder->label('X'));
        self::assertSame($builder, $builder->description('desc'));
        self::assertSame($builder, $builder->timestamps());
        self::assertSame($builder, $builder->softDeletes());
        self::assertSame($builder, $builder->field('a', FieldType::String));
        self::assertSame($builder, $builder->belongsTo('b'));
        self::assertSame($builder, $builder->hasMany('c'));
        self::assertSame($builder, $builder->hasOne('d'));
        self::assertSame($builder, $builder->belongsToMany('e'));
        self::assertSame($builder, $builder->morphTo('f'));
        self::assertSame($builder, $builder->morphMany('g'));
        self::assertSame($builder, $builder->computed('h', FieldType::Integer, via: 'count:x'));
        self::assertSame($builder, $builder->filterable(['a']));
        self::assertSame($builder, $builder->sortable(['a']));
        self::assertSame($builder, $builder->searchable(['a']));
        self::assertSame($builder, $builder->action('create'));
        self::assertSame($builder, $builder->meta(['k' => 'v']));
    }
}

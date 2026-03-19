<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Tests\Definition;

use DanDoeTech\ResourceRegistry\Contracts\ActionDefinitionInterface;
use DanDoeTech\ResourceRegistry\Definition\ActionDefinition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ActionDefinitionTest extends TestCase
{
    #[Test]
    public function implementsInterface(): void
    {
        $action = new ActionDefinition(name: 'create');

        self::assertInstanceOf(ActionDefinitionInterface::class, $action);
    }

    #[Test]
    public function allGettersReturnConstructorValues(): void
    {
        $action = new ActionDefinition(
            name: 'deploy',
            description: 'Deploy to production',
            meta: ['confirm' => true, 'icon' => 'rocket'],
            handler: 'App\\Actions\\DeployHandler',
        );

        self::assertSame('deploy', $action->getName());
        self::assertSame('Deploy to production', $action->getDescription());
        self::assertSame(['confirm' => true, 'icon' => 'rocket'], $action->getMeta());
        self::assertSame('App\\Actions\\DeployHandler', $action->getHandler());
    }

    #[Test]
    public function defaultValuesAreCorrect(): void
    {
        $action = new ActionDefinition(name: 'create');

        self::assertNull($action->getDescription());
        self::assertSame([], $action->getMeta());
        self::assertNull($action->getHandler());
    }

    #[Test]
    public function crudActionsHaveNullHandler(): void
    {
        foreach (['create', 'update', 'delete'] as $name) {
            $action = new ActionDefinition(name: $name);
            self::assertNull($action->getHandler(), "CRUD action '{$name}' should have null handler");
        }
    }

    #[Test]
    public function customActionCanHaveHandler(): void
    {
        $action = new ActionDefinition(
            name: 'activate',
            handler: 'App\\Actions\\ActivateHandler',
        );

        self::assertSame('App\\Actions\\ActivateHandler', $action->getHandler());
    }
}

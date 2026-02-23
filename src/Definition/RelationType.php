<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

enum RelationType: string
{
    case HasOne = 'has_one';
    case HasMany = 'has_many';
    case BelongsTo = 'belongs_to';
    case BelongsToMany = 'belongs_to_many';
    case HasManyThrough = 'has_many_through';
    case HasOneThrough = 'has_one_through';
    case ManyToMany = 'many_to_many';
}

<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

enum RelationType: string
{
    case BelongsTo = 'belongs_to';
    case HasMany = 'has_many';
    case HasOne = 'has_one';
    case ManyToMany = 'many_to_many';
}

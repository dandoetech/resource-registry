<?php

declare(strict_types=1);

namespace DanDoeTech\ResourceRegistry\Definition;

enum FieldType: string
{
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';
    case Boolean = 'boolean';
    case DateTime = 'datetime';
    case Json = 'json';
}

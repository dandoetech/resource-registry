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
    case Date = 'date';
    case Text = 'text';
    case Email = 'email';
    case Url = 'url';
    case Enum = 'enum';
}

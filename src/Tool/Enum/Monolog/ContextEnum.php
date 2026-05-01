<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Monolog;

enum ContextEnum: string
{
    case MODULE = 'module';
    case DURATION = 'duration';
    case CLASS_NAME = 'class-name';
    case EXCEPTION = 'exception';
}

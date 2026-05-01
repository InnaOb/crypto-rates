<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Monolog;

enum ModuleContextEnum: string
{
    case BINANCE_HTTP_CLIENT = 'BINANCE_HTTP_CLIENT';
    case SCHEDULER = 'SCHEDULER';
    case EXCEPTION = 'EXCEPTION';
}

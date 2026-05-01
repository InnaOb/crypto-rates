<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Logger;

enum LogMessageEnum: string
{
    case STARTED = 'STARTED';
    case FINISHED = 'FINISHED';
}

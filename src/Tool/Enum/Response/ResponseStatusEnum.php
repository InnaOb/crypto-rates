<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Response;

enum ResponseStatusEnum: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
}

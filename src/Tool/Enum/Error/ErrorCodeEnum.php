<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Error;

enum ErrorCodeEnum: string
{
    case UNHANDLED_ERROR = 'cr_error_1';
    case VALIDATION_ERROR = 'cr_error_2';
    case NOT_FOUND_ERROR = 'cr_error_3';
    case SERVICE_UNAVAILABLE_ERROR = 'cr_error_4';
}

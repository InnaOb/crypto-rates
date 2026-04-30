<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Enum\Error;

enum ErrorMessageEnum: string
{
    case UNHANDLED_ERROR = 'Unhandled error.';
    case VALIDATION_ERROR = 'Validation error.';
    case NOT_FOUND_ERROR = 'Not found.';
    case SERVICE_UNAVAILABLE_ERROR = 'Service unavailable.';
}

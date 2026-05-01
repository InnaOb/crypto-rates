<?php

declare(strict_types=1);

namespace CryptoRate\FrameworkLayer\Exception;

use CryptoRate\Tool\Enum\Error\ErrorCodeEnum;
use CryptoRate\Tool\Enum\Error\ErrorMessageEnum;
use Throwable;

final class ServiceUnavailableCustomException extends BaseCustomRuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(
            message: $message,
            errorCodeEnum: ErrorCodeEnum::SERVICE_UNAVAILABLE_ERROR,
            errorMessageEnum: ErrorMessageEnum::SERVICE_UNAVAILABLE_ERROR,
            previous: $previous,
        );
    }
}

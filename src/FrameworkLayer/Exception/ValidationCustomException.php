<?php

declare(strict_types=1);

namespace CryptoRate\FrameworkLayer\Exception;

use CryptoRate\Tool\Enum\Error\ErrorCodeEnum;
use CryptoRate\Tool\Enum\Error\ErrorMessageEnum;
use Throwable;

final class ValidationCustomException extends BaseCustomRuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct(
            message: $message,
            errorCodeEnum: ErrorCodeEnum::VALIDATION_ERROR,
            errorMessageEnum: ErrorMessageEnum::VALIDATION_ERROR,
            previous: $previous,
        );
    }
}

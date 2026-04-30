<?php

declare(strict_types=1);

namespace CryptoRate\FrameworkLayer\Exception;

use CryptoRate\Tool\Enum\Error\ErrorCodeEnum;
use CryptoRate\Tool\Enum\Error\ErrorMessageEnum;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class BaseCustomRuntimeException extends RuntimeException implements BaseCustomRuntimeExceptionInterface
{
    public function __construct(
        string $message,
        private readonly ErrorCodeEnum $errorCodeEnum,
        private readonly ErrorMessageEnum $errorMessageEnum,
        ?Throwable $previous = null,
        private readonly int $httpStatusCode = Response::HTTP_OK,
    ) {
        parent::__construct($message, $httpStatusCode, $previous);
    }

    public function getErrorCodeEnum(): ErrorCodeEnum
    {
        return $this->errorCodeEnum;
    }

    public function getErrorMessageEnum(): ErrorMessageEnum
    {
        return $this->errorMessageEnum;
    }

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }
}

<?php

declare(strict_types=1);

namespace CryptoRate\FrameworkLayer\Exception;

use CryptoRate\Tool\Enum\Error\ErrorCodeEnum;
use CryptoRate\Tool\Enum\Error\ErrorMessageEnum;

interface BaseCustomRuntimeExceptionInterface
{
    public function getErrorCodeEnum(): ErrorCodeEnum;

    public function getErrorMessageEnum(): ErrorMessageEnum;

    public function getHttpStatusCode(): int;
}

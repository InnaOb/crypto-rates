<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Response\General\Error;

use CryptoRate\Tool\Enum\Error\ErrorCodeEnum;
use CryptoRate\Tool\Enum\Error\ErrorMessageEnum;

final class UnhandledErrorResponse extends BaseErrorResponse
{
    public function __construct()
    {
        parent::__construct(
            details: 'An unexpected error occurred.',
        );
    }
}

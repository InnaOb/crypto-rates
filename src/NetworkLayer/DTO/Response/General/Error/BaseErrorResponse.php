<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Response\General\Error;

use CryptoRate\NetworkLayer\DTO\Response\General\BaseResponseInterface;
use CryptoRate\Tool\Enum\Error\ErrorCodeEnum;
use CryptoRate\Tool\Enum\Error\ErrorMessageEnum;
use CryptoRate\Tool\Enum\Response\ResponseStatusEnum;
use CryptoRate\Tool\Helper\StringHelper;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[OA\Schema(description: 'Error response')]
class BaseErrorResponse implements BaseResponseInterface
{
    public function __construct(
        #[OA\Property(description: 'Response status', type: 'string', example: 'error', enum: ['error'])]
        public readonly ResponseStatusEnum $status = ResponseStatusEnum::ERROR,

        #[SerializedName('error_code')]
        #[OA\Property(property: 'error_code', description: 'Application error code', type: 'string', example: 'cr_error_2')]
        public readonly ErrorCodeEnum $errorCode = ErrorCodeEnum::UNHANDLED_ERROR,

        #[OA\Property(description: 'Error message', type: 'string', example: 'Validation error.')]
        public readonly ErrorMessageEnum $message = ErrorMessageEnum::UNHANDLED_ERROR,

        #[OA\Property(description: 'Error details', type: 'string', example: 'pair: This value should not be blank.')]
        public readonly string $details = StringHelper::EMPTY_STRING,
    ) {
    }
}

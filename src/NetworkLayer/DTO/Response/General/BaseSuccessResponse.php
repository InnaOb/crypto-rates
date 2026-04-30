<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Response\General;

use CryptoRate\Tool\Enum\Response\ResponseStatusEnum;
use OpenApi\Attributes as OA;

class BaseSuccessResponse implements BaseResponseInterface
{
    public function __construct(
        public readonly ?ResponseDataInterface $data = null,

        #[OA\Property(description: 'Response status', type: 'string', example: 'success', enum: ['success'])]
        public readonly ResponseStatusEnum $status = ResponseStatusEnum::SUCCESS,
    ) {
    }
}

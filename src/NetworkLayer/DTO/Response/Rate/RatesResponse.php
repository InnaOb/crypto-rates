<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Response\Rate;

use CryptoRate\NetworkLayer\DTO\Response\General\BaseSuccessResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Success response with rate data')]
final class RatesResponse extends BaseSuccessResponse
{
    public function __construct(
        #[OA\Property(ref: new Model(type: RatesData::class))]
        RatesData $data,
    ) {
        parent::__construct(data: $data);
    }
}

<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\DTO\Response\Rate;

use CryptoRate\NetworkLayer\DTO\Response\General\ResponseDataInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(description: 'Rate data payload')]
final readonly class RatesData implements ResponseDataInterface
{
    /**
     * @param RateItemResponse[] $items
     */
    public function __construct(
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: RateItemResponse::class)))]
        public array $items,
    ) {
    }

    /**
     * @param RateItemResponse[] $items
     */
    public static function fromItems(array $items): self
    {
        return new self(items: $items);
    }
}

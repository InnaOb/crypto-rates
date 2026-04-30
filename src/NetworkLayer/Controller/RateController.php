<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\Controller;

use CryptoRate\NetworkLayer\DTO\Request\Rate\DayRateRequest;
use CryptoRate\NetworkLayer\DTO\Request\Rate\Last24hRequest;
use CryptoRate\NetworkLayer\DTO\Response\General\Error\BaseErrorResponse;
use CryptoRate\NetworkLayer\DTO\Response\Rate\RatesResponse;
use CryptoRate\NetworkLayer\Factory\RatesResponseFactory;
use CryptoRate\ServiceLayer\Provider\CryptoRate\CryptoRateProviderInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/rates')]
#[OA\Tag(name: 'Crypto Rates')]
#[OA\Response(
    response: '200-error',
    description: 'Validation or service error',
    content: new OA\JsonContent(ref: new Model(type: BaseErrorResponse::class))
)]
final class RateController extends AbstractCustomController
{
    public function __construct(
        private readonly CryptoRateProviderInterface $cryptoRateProvider,
        private readonly RatesResponseFactory $ratesResponseFactory,
    ) {
    }

    #[Route('/last-24h', name: 'api_rates_last_24h', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get rates for the last 24 hours (every 5 minutes)',
        parameters: [
            new OA\Parameter(
                name: 'pair',
                description: 'Currency pair',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['EUR/BTC', 'EUR/ETH', 'EUR/LTC']),
                example: 'EUR/BTC',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of rates for the last 24 hours',
                content: new OA\JsonContent(ref: new Model(type: RatesResponse::class))
            ),
        ]
    )]
    public function last24h(Request $request): JsonResponse
    {
        $last24Request = Last24hRequest::fromQuery($request->query->all());
        $this->validateAndThrow($last24Request);

        $rates = $this->cryptoRateProvider->getLast24h($last24Request->getPairEnum());

        return $this->successResponse($this->ratesResponseFactory->fromRates($rates));
    }

    #[Route('/day', name: 'api_rates_day', methods: ['GET'])]
    #[OA\Get(
        summary: 'Get rates for a specific day (every 5 minutes)',
        parameters: [
            new OA\Parameter(
                name: 'pair',
                description: 'Currency pair',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['EUR/BTC', 'EUR/ETH', 'EUR/LTC']),
                example: 'EUR/BTC',
            ),
            new OA\Parameter(
                name: 'date',
                description: 'Date in YYYY-MM-DD format',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', format: 'date'),
                example: '2026-04-30',
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of rates for the given day',
                content: new OA\JsonContent(ref: new Model(type: RatesResponse::class))
            ),
        ]
    )]
    public function day(Request $request): JsonResponse
    {
        $dateRateRequest = DayRateRequest::fromQuery($request->query->all());
        $this->validateAndThrow($dateRateRequest);

        $rates = $this->cryptoRateProvider->getByDay($dateRateRequest->getPairEnum(), $dateRateRequest->getDateAsImmutable());

        return $this->successResponse($this->ratesResponseFactory->fromRates($rates));
    }
}

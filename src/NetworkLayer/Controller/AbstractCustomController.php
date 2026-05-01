<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\Controller;

use CryptoRate\NetworkLayer\DTO\Response\General\BaseSuccessResponse;
use CryptoRate\Tool\Util\SerializerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractCustomController extends AbstractController
{
    use SerializerAwareTrait;

    protected function successResponse(BaseSuccessResponse $response): JsonResponse
    {
        return new JsonResponse(
            data: $this->normalize($response),
        );
    }
}

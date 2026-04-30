<?php

declare(strict_types=1);

namespace CryptoRate\NetworkLayer\Controller;

use CryptoRate\NetworkLayer\DTO\Response\General\BaseSuccessResponse;
use CryptoRate\NetworkLayer\DTO\Response\General\ResponseDataInterface;
use CryptoRate\Tool\Util\SerializerAwareTrait;
use CryptoRate\Tool\Util\ValidatorAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractCustomController extends AbstractController
{
    use ValidatorAwareTrait;
    use SerializerAwareTrait;

    protected function successResponse(BaseSuccessResponse $response): JsonResponse
    {
        return new JsonResponse(
            data: $this->normalize($response),
        );
    }
}

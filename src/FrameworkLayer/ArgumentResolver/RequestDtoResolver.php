<?php

declare(strict_types=1);

namespace CryptoRate\FrameworkLayer\ArgumentResolver;

use CryptoRate\NetworkLayer\DTO\Request\RequestDtoInterface;
use CryptoRate\Tool\Util\ValidatorAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestDtoResolver implements ValueResolverInterface
{
    use ValidatorAwareTrait;

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if (!$type || !is_a($type, RequestDtoInterface::class, true)) {
            return [];
        }

        /** @var class-string<RequestDtoInterface> $type */
        $dto = $type::fromQuery($request->query->all());

        $this->validateAndThrow($dto);

        yield $dto;
    }
}

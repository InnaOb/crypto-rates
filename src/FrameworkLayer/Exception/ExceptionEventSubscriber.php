<?php

declare(strict_types=1);

namespace CryptoRate\FrameworkLayer\Exception;

use CryptoRate\NetworkLayer\DTO\Response\General\Error\BaseErrorResponse;
use CryptoRate\NetworkLayer\DTO\Response\General\Error\UnhandledErrorResponse;
use CryptoRate\Tool\Enum\Monolog\ContextEnum;
use CryptoRate\Tool\Enum\Monolog\ModuleContextEnum;
use CryptoRate\Tool\Util\SerializerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

final class ExceptionEventSubscriber implements EventSubscriberInterface
{
    use SerializerAwareTrait;

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof BaseCustomRuntimeExceptionInterface) {
            $this->logException($exception);
            $event->setResponse($this->buildErrorResponse($exception));
        } else {
            $this->logUnhandledException($exception);
            $event->setResponse($this->buildUnhandledErrorResponse());
        }

        $event->allowCustomResponseCode();
    }

    private function buildErrorResponse(BaseCustomRuntimeExceptionInterface $exception): JsonResponse
    {
        $response = new BaseErrorResponse(
            errorCode: $exception->getErrorCodeEnum(),
            message: $exception->getErrorMessageEnum(),
            details: $exception->getMessage(),
        );

        return new JsonResponse(
            data: $this->normalize($response),
            status: $exception->getHttpStatusCode(),
        );
    }

    private function buildUnhandledErrorResponse(): JsonResponse
    {
        return new JsonResponse(
            data: $this->normalize(new UnhandledErrorResponse()),
            status: Response::HTTP_OK,
        );
    }

    private function logException(BaseCustomRuntimeExceptionInterface&Throwable $exception): void
    {
        $this->logger->error(
            $exception->getMessage(),
            [
                ContextEnum::MODULE->value => ModuleContextEnum::EXCEPTION->value,
                ContextEnum::EXCEPTION->value => $exception,
            ],
        );
    }

    private function logUnhandledException(Throwable $exception): void
    {
        $this->logger->critical(
            $exception->getMessage(),
            [
                ContextEnum::MODULE->value => ModuleContextEnum::EXCEPTION->value,
                ContextEnum::EXCEPTION->value => $exception,
            ],
        );
    }
}

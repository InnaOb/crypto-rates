<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Util;

use CryptoRate\DataLayer\Object\HttpClient\Request\BaseRequestOption;
use CryptoRate\Tool\Enum\Logger\LogMessageEnum;
use CryptoRate\Tool\Enum\Monolog\ContextEnum;
use CryptoRate\Tool\Enum\Monolog\ModuleContextEnum;
use CryptoRate\Tool\Helper\StringHelper;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait LoggerAwareTrait
{
    protected readonly LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function getClassName(): string
    {
        return StringHelper::getShortClassName(static::class);
    }

    protected function logHttpClientRequest(
        string $httpClientName,
        BaseRequestOption $requestOption,
        string $requestBody
    ): void {
        $this->logger->info(
            sprintf(
                "[%s] Request.\nURL: %s\nMethod: %s\nBody: %s",
                $httpClientName,
                $requestOption->getFullUrl(),
                $requestOption->getRequestMethodString(),
                $requestBody
            ),
            [ContextEnum::MODULE->value => $httpClientName]
        );
    }

    protected function logHttpClientResponse(
        string $httpClientName,
        ResponseInterface $httpClientResponse,
        ?string $responseContent = null,
        ?float $requestDuration = null
    ): void {
        $headers = $httpClientResponse->getHeaders(false);

        $this->logger->info(
            sprintf(
                "[%s] Response.\nURL: %s\nCode: %d\nContent-Type: %s\nBody: %s",
                $httpClientName,
                $httpClientResponse->getInfo('url'),
                $httpClientResponse->getInfo('http_code'),
                $headers['content-type'][0] ?? StringHelper::EMPTY_STRING,
                $responseContent ?? StringHelper::EMPTY_STRING
            ),
            [
                ContextEnum::MODULE->value => $httpClientName,
                ContextEnum::DURATION->value => $requestDuration ?? 0.0,
            ]
        );
    }

    protected function logFinished(ModuleContextEnum $moduleContext): void
    {
        $this->logger->info(
            LogMessageEnum::FINISHED->value,
            [
                ContextEnum::MODULE->value => $moduleContext->value,
                ContextEnum::CLASS_NAME->value => $this->getClassName(),
            ]
        );
    }

    protected function logStarted(ModuleContextEnum $moduleContext): void
    {
        $this->logger->info(
            LogMessageEnum::STARTED->value,
            [
                ContextEnum::MODULE->value => $moduleContext->value,
                ContextEnum::CLASS_NAME->value => $this->getClassName(),
            ]
        );
    }
}

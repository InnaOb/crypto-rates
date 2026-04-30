<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Util;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait SerializerAwareTrait
{
    protected readonly SerializerInterface $serializer;

    #[Required]
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    protected function normalize(mixed $data): mixed
    {
        return $this->serializer->normalize($data);
    }
}

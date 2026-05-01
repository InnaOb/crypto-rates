<?php

declare(strict_types=1);

namespace CryptoRate\Tool\Helper;

final readonly class StringHelper
{
    public const EMPTY_STRING = '';

    public static function getShortClassName(string $classNameWithNamespace): string
    {
        $lastNamespacePosition = strrpos($classNameWithNamespace, '\\');

        if (false === $lastNamespacePosition) {
            return $classNameWithNamespace;
        }

        return substr($classNameWithNamespace, $lastNamespacePosition + 1);
    }
}

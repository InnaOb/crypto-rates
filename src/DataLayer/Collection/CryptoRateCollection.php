<?php

declare(strict_types=1);

namespace CryptoRate\DataLayer\Collection;

use ArrayIterator;
use Countable;
use CryptoRate\DataLayer\Entity\CryptoRate;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, CryptoRate>
 */
final readonly class CryptoRateCollection implements IteratorAggregate, Countable
{
    /**
     * @param CryptoRate[] $items
     */
    private function __construct(private array $items)
    {
    }

    /**
     * @param CryptoRate[] $items
     */
    public static function fromEntities(array $items): self
    {
        return new self($items);
    }

    public static function empty(): self
    {
        return new self([]);
    }

    public function withAdded(CryptoRate $rate): self
    {
        return new self([...$this->items, $rate]);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function isEmpty(): bool
    {
        return $this->items === [];
    }

    public function isNotEmpty(): bool
    {
        return $this->items !== [];
    }

    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }
}

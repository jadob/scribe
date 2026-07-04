<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate\Id;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidBinaryAggregateId implements AggregateRootIdInterface
{
    public function __construct(
        private UuidInterface $uuid,
    ) {
    }

    public static function new7(): self
    {
        return new self(Uuid::uuid7());
    }

    public static function fromString(string $id): AggregateRootIdInterface
    {
        return new self(Uuid::fromBytes($id));
    }

    public function toString(): string
    {
        return $this
            ->uuid
            ->getBytes();
    }
}

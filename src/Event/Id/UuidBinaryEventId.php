<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event\Id;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidBinaryEventId implements EventIdInterface
{
    public function __construct(
        private UuidInterface $uuid,
    ) {
    }

    public static function new7(): self
    {
        return new self(Uuid::uuid7());
    }

    public static function fromString(string $eventId): self
    {
        return new self(Uuid::fromBytes($eventId));
    }

    public function toString(): string
    {
        return $this
            ->uuid
            ->getBytes();
    }
}

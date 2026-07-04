<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event\Id;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidEventId implements EventIdInterface
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
        return new self(Uuid::fromString($eventId));
    }

    public function __toString(): string
    {
        return $this
            ->uuid
            ->toString();
    }
}

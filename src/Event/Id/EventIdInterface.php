<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event\Id;

interface EventIdInterface
{
    public function toString(): string;

    public static function fromString(string $eventId): self;
}

<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event;

interface EventInterface
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function reconstitute(
        array $payload,
    ): static;
}

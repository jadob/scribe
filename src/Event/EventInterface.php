<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event;

use Jadob\Scribe\Event\Id\EventIdInterface;

interface EventInterface
{
    public function getEventId(): EventIdInterface;

    public static function reconstitute(
        string $eventId,
        array $payload
    ): self;
}

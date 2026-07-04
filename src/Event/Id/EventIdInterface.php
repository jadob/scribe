<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event\Id;

use Stringable;

interface EventIdInterface extends Stringable
{
    public static function fromString(string $eventId): self;
}

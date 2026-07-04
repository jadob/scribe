<?php

declare(strict_types=1);

namespace Jadob\Scribe\Fixtures\Event;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Event\EventPayload;
use Jadob\Scribe\Event\Id\EventIdInterface;
use Jadob\Scribe\Event\Id\UuidBinaryEventId;

final class UserCreatedEvent implements EventInterface
{
    private EventIdInterface $id;

    public function __construct(
        private(set) readonly AggregateRootIdInterface $userId,
        private(set) readonly string $username,
        #[EventPayload(encrypted: true)] private(set) readonly string $email,
    ) {
        $this->id = UuidBinaryEventId::new7();
    }

    public function getEventId(): EventIdInterface
    {
        return $this->id;
    }
}

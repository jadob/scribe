<?php

declare(strict_types=1);

namespace Jadob\Scribe\Fixtures\Event;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Aggregate\Id\UuidAggregateId;
use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Event\EventPayload;
use Jadob\Scribe\Event\Id\EventIdInterface;
use Jadob\Scribe\Event\Id\UuidEventId;

final class UserCreatedEvent implements EventInterface
{
    public function __construct(
        private(set) readonly EventIdInterface $id,
        private(set) readonly AggregateRootIdInterface $userId,
        private(set) readonly string $username,
        #[EventPayload(encrypted: true)] private(set) readonly string $email,
    ) {
    }

    public function getEventId(): EventIdInterface
    {
        return $this->id;
    }

    /**
     * @param array{userId: non-empty-string, username: non-empty-string, email: non-empty-string} $payload
     */
    public static function reconstitute(string $eventId, array $payload): self
    {
        return new self(
            id: UuidEventId::fromString($eventId),
            userId: UuidAggregateId::fromString($payload['userId']),
            username: $payload['username'],
            email: $payload['email'],
        );
    }
}

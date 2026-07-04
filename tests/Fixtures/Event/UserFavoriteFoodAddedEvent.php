<?php

declare(strict_types=1);

namespace Jadob\Scribe\Fixtures\Event;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Aggregate\Id\UuidAggregateId;
use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Event\Id\EventIdInterface;
use Jadob\Scribe\Event\Id\UuidEventId;

final readonly class UserFavoriteFoodAddedEvent implements EventInterface
{
    public function __construct(
        private(set) EventIdInterface $eventId,
        private(set) AggregateRootIdInterface $userId,
        private(set) string $favoriteFoodName,
    ) {
    }

    public function getEventId(): EventIdInterface
    {
        return $this->eventId;
    }

    /**
     * @param array{userId: non-empty-string, favoriteFoodName: non-empty-string} $payload
     */
    public static function reconstitute(string $eventId, array $payload): self
    {
        return new self(
            eventId: UuidEventId::fromString($eventId),
            userId: UuidAggregateId::fromString($payload['userId']),
            favoriteFoodName: $payload['favoriteFoodName'],
        );
    }
}

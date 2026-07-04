<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Event\EventInterface;

interface AggregateRootInterface
{
    public function getAggregateRootId(): AggregateRootIdInterface;

    /**
     * @return array<int, EventInterface>
     */
    public function popEvents(): array;

    /**
     * @param array<EventInterface> $events
     */
    public static function recreate(
        AggregateRootIdInterface $aggregateRootId,
        array $events,
    ): self;
}

<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;

interface AggregateRootInterface
{
    public function getAggregateRootId(): AggregateRootIdInterface;

    /**
     * @return array<int, object>
     */
    public function popEvents(): array;

    /**
     * @param array<object> $events
     */
    public static function recreate(
        AggregateRootIdInterface $aggregateRootId,
        array $events,
    ): self;
}

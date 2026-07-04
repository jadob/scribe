<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Message\Message;

interface MessageRepositoryInterface
{
    /**
     * @param Message<EventInterface> ...$message
     */
    public function store(
        Message ...$message,
    ): void;

    /**
     * @return array<Message<EventInterface>>
     */
    public function load(
        AggregateRootIdInterface $aggregateRootId,
    ): array;
}

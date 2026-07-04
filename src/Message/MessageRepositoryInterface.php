<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;

interface MessageRepositoryInterface
{
    /**
     * @param Message<EventInterface> ...$message
     */
    public function store(
        Message ...$message,
    ): void;

    /**
     * @return Message
     */
    public function load(
        AggregateRootIdInterface $aggregateRootId,
    ): array;
}

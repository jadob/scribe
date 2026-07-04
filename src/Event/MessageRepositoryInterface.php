<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Message\Message;

interface MessageRepositoryInterface
{
    public function store(
        Message ...$message
    ): void;

    /**
     * @return array<Message>
     */
    public function load(
        AggregateRootIdInterface $aggregateRootId
    ): array;
}

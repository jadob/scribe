<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use Jadob\Scribe\EventDispatcher\EventDispatcherInterface;
use Jadob\Scribe\Message\MessageRepositoryInterface;

/**
 * @template T of AggregateRootInterface
 */
final readonly class AggregateRootRepositoryFactory
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * @param class-string<T> $aggregateClass
     *
     * @return AggregateRootRepository<T>
     */
    public function createFor(
        string $aggregateClass,
    ): AggregateRootRepository {
        return new AggregateRootRepository(
            $aggregateClass,
            $this->messageRepository,
            $this->eventDispatcher
        );
    }
}

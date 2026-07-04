<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use Jadob\Scribe\Event\MessageRepositoryInterface;

/**
 * @template T of AggregateRootInterface
 */
final readonly class AggregateRootRepositoryFactory
{
    public function __construct(
        private MessageRepositoryInterface $messageRepository,
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
        );
    }
}

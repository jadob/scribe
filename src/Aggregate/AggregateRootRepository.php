<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use DateTime;
use Jadob\Scribe\Aggregate\Exception\NoEventsRecordedException;
use Jadob\Scribe\Aggregate\Exception\UnsupportedAggregateTypeException;
use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;
use Jadob\Scribe\Message\MessageRepositoryInterface;
use function count;
use function get_class;

/**
 * @template T of AggregateRootInterface
 */
readonly class AggregateRootRepository
{
    /**
     * @param class-string<T> $aggregateRootClass
     */
    public function __construct(
        private string $aggregateRootClass,
        private MessageRepositoryInterface $messageRepository,
    ) {
    }

    /**
     * @throws UnsupportedAggregateTypeException
     * @throws NoEventsRecordedException
     */
    public function store(AggregateRootInterface $aggregateRoot): void
    {
        $aggregateFqcn = get_class($aggregateRoot);
        $this->assertAggregateType($aggregateFqcn);
        $aggregateId = $aggregateRoot->getAggregateRootId();
        $events = $aggregateRoot->popEvents();

        if (count($events) === 0) {
            throw new NoEventsRecordedException(sprintf('No events found for aggregate %s', $aggregateFqcn));
        }

        $messages = [];
        foreach ($events as $revision => $event) {
            $messages[] = Message::create($event)
                ->withHeader(MessageHeader::AGGREGATE_ID, (string) $aggregateId)
                ->withHeader(MessageHeader::AGGREGATE_REVISION, $revision)
                ->withHeader(MessageHeader::AGGREGATE_TYPE, $aggregateFqcn)
                ->withHeader(MessageHeader::RECORDED_AT, new DateTime()->getTimestamp());
        }

        $this
            ->messageRepository
            ->store(...$messages);
    }

    /**
     * @throws UnsupportedAggregateTypeException
     */
    private function assertAggregateType(
        string $aggregateRootClass,
    ): void {
        if ($this->aggregateRootClass !== $aggregateRootClass) {
            throw new UnsupportedAggregateTypeException(sprintf('Invalid aggregate type (received "%s", repository configured for "%s")', $aggregateRootClass, $this->aggregateRootClass));
        }
    }
}

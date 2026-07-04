<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;

abstract class AbstractAggregate implements AggregateRootInterface
{
    protected AggregateRootIdInterface $aggregateId;

    private int $aggregateRevision = 0;

    /**
     * @var array<int, object>
     */
    private array $recordedEvents = [];

    protected function recordThat(object $event): void
    {
        ++$this->aggregateRevision;
        $this->recordedEvents[$this->aggregateRevision] = $event;
        $this->handle($event);
    }

    public function getAggregateRootId(): AggregateRootIdInterface
    {
        return $this->aggregateId;
    }

    /**
     * @return array<int, object>
     */
    public function popEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    public static function recreate(
        AggregateRootIdInterface $aggregateRootId,
        array $events
    ): self {
        $self = new static();
        $self->aggregateId = $aggregateRootId;
        foreach ($events as $event) {
            $self->handle($event);
        }

        return $self;
    }

    abstract protected function handle(object $event): void;
}

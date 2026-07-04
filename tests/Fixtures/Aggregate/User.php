<?php

declare(strict_types=1);

namespace Jadob\Scribe\Fixtures\Aggregate;

use Jadob\Scribe\Aggregate\AbstractAggregate;
use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Fixtures\Event\UserCreatedEvent;

final class User extends AbstractAggregate
{
    public static function create(
        AggregateRootIdInterface $id,
        string $username,
        string $email,
    ): self {
        $self = new self();

        $self->recordThat(
            new UserCreatedEvent(
                $id,
                $username,
                $email
            )
        );

        return $self;
    }

    protected function handle(EventInterface $event): void
    {
        if ($event instanceof UserCreatedEvent) {
            $this->aggregateId = $event->userId;
        }
    }
}

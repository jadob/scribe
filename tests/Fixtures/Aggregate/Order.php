<?php

declare(strict_types=1);

namespace Jadob\Scribe\Fixtures\Aggregate;

use Jadob\Scribe\Aggregate\AbstractAggregate;
use Jadob\Scribe\Event\EventInterface;

class Order extends AbstractAggregate
{
    protected function handle(EventInterface $event): void
    {
    }
}

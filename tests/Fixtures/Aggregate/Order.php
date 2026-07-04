<?php

declare(strict_types=1);

namespace Jadob\Scribe\Fixtures\Aggregate;

use Jadob\Scribe\Aggregate\AbstractAggregate;

class Order extends AbstractAggregate
{
    protected function handle(object $event): void
    {
    }
}

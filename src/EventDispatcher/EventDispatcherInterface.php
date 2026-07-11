<?php

declare(strict_types=1);

namespace Jadob\Scribe\EventDispatcher;

use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Message\Message;

interface EventDispatcherInterface
{
    /**
     * @param object ...$events
     */
    public function dispatch(object ...$event): void;
}

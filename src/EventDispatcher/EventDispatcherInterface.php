<?php

declare(strict_types=1);

namespace Jadob\Scribe\EventDispatcher;

use Jadob\Scribe\Message\Message;

interface EventDispatcherInterface
{
    public function dispatch(Message ...$messages): void;
}

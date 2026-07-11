<?php

namespace Jadob\Scribe\EventDispatcher;

use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Message\Message;
use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

class PsrEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private PsrEventDispatcherInterface $dispatcher,
    )
    {
    }

    public function dispatch(object ...$events): void
    {
        foreach ($events as $event) {
            $this
                ->dispatcher
                ->dispatch($event);
        }
    }

}

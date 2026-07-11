<?php

declare(strict_types=1);

namespace Jadob\Scribe\EventDispatcher;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

class PsrEventDispatcher implements EventDispatcherInterface
{
    public function __construct(
        private PsrEventDispatcherInterface $dispatcher,
    ) {
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

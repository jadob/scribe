<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

final readonly class PassthroughEventNameInflector implements EventNameInflectorInterface
{
    /**
     * @param class-string $fqcn
     *
     * @return class-string
     */
    public function fromFqcn(string $fqcn): string
    {
        return $fqcn;
    }

    /**
     * @param class-string $eventName
     *
     * @return class-string
     */
    public function toFqcn(string $eventName): string
    {
        return $eventName;
    }
}

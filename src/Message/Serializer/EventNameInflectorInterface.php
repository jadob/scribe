<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

/**
 * It is entirely up to message serialization implementation whether this contract can be respected.
 */
interface EventNameInflectorInterface
{
    /**
     * @param class-string $fqcn
     */
    public function fromFqcn(
        string $fqcn,
    ): string;

    /**
     * @return class-string
     */
    public function toFqcn(
        string $eventName
    ): string;
}

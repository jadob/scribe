<?php

namespace Jadob\Scribe\Message\Serializer;

/**
 * It is entirely up to message serialization implementation whether this contract can be respected.
 */
interface EventNameInflectorInterface
{
    /**
     * @param class-string $fqcn
     * @return string
     */
    public function fromFqcn(
        string $fqcn,
    ): string;

    /**
     * @param string $eventName
     * @return class-string
     */
    public function toFqcn(
        string $eventName
    ): string;

}
<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Message\Message;

/**
 * @template T of EventInterface
 *
 * @psalm-type MessagePayload array{headers: array<non-empty-string, string|int>, payload: array<string,mixed>}
 */
interface MessageNormalizerInterface
{
    /**
     * @param Message<T> $message
     *
     * @return MessagePayload
     */
    public function normalize(Message $message): array;

    /**
     * @param MessagePayload  $message
     * @param class-string<T> $eventFqcn
     *
     * @return Message<T>
     */
    public function denormalize(
        array $message,
        string $eventId,
        string $eventFqcn
    ): Message;
}

<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Message\Message;

/**
 * @psalm-type MessagePayload array{headers: array<non-empty-string, string|int>, payload: array<string,mixed>}
 */
interface MessageNormalizerInterface
{
    /**
     * @return MessagePayload
     */
    public function normalize(Message $message): array;

    /**
     * @param MessagePayload $message
     */
    public function denormalize(
        array $message,
        string $eventId,
        string $eventFqcn
    ): Message;
}

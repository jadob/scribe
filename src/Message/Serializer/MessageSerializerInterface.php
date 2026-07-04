<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Message\Message;

/**
 * @psalm-type MessagePayload array{headers: array<non-empty-string, string|int>, payload: array<string,mixed>}
 */
interface MessageSerializerInterface
{
    /**
     * @return MessagePayload
     */
    public function serialize(Message $message): array;

    /**
     * @param MessagePayload $message
     */
    public function unserialize(array $message): Message;
}

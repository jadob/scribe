<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Message\Message;

interface MessageSerializerInterface
{
    public function serialize(Message $message): string;

    public function deserialize(string $payload): Message;
}

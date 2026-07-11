<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;

use function get_class;
use function json_decode;
use function json_encode;
use const JSON_THROW_ON_ERROR;

final readonly class MessageSerializer implements MessageSerializerInterface
{
    public function __construct(
        private MessageNormalizerInterface $normalizer,
        private EventNameInflectorInterface $inflector,
    ) {
    }

    public function serialize(Message $message): string
    {
        $event = $message->event;
        $eventType = $this
            ->inflector
            ->fromFqcn(get_class($event));

        $message = $message
            ->withHeader(MessageHeader::EVENT_TYPE, $eventType);

        $normalizedMessage = $this
            ->normalizer
            ->normalize(
                $message
            );

        return json_encode(
            $normalizedMessage,
            JSON_THROW_ON_ERROR
        );
    }

    public function deserialize(string $payload): Message
    {
        $data = json_decode(
            $payload,
            true,
            flags: JSON_THROW_ON_ERROR
        );

        $eventType = $data['headers'][MessageHeader::EVENT_TYPE];
        $eventFqcn = $this
            ->inflector
            ->fromFqcn($eventType);

        return $this
            ->normalizer
            ->denormalize(
                $data,
                $eventFqcn,
            );
    }
}

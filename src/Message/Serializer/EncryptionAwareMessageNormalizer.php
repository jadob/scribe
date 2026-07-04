<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Message\Encryption\EventEncryptionProviderInterface;
use Jadob\Scribe\Message\Message;
use LogicException;
use ReflectionClass;
use Stringable;

use function get_class;
use function is_object;
use function sprintf;

/**
 * @template T of EventInterface
 * @implements MessageNormalizerInterface<T>
 * @psalm-import-type MessagePayload from MessageNormalizerInterface
 */
final readonly class EncryptionAwareMessageNormalizer implements MessageNormalizerInterface
{
    public function __construct(
        private EventEncryptionProviderInterface $eventEncryptionProvider,
    ) {
    }

    /**
     * @param Message<T> $message
     * @return MessagePayload
     */
    public function normalize(Message $message): array
    {
        $eventPayload = [];
        $event = $message->event;
        $eventReflection = new ReflectionClass($event);

        foreach ($eventReflection->getProperties() as $property) {
            $val = $property->getValue($event);
            $key = $property->getName();

            if ($val instanceof Stringable) {
                $val = (string) $val;
            }

            $eventPayload[$key] = $val;
        }

        return [
            'headers' => $message->headers,
            'payload' => $eventPayload,
        ];
    }

    /**
     * @param MessagePayload    $message
     * @param class-string<T>   $eventFqcn
     *
     * @return Message<T>
     */
    public function denormalize(
        array $message,
        string $eventId,
        string $eventFqcn,
    ): Message {
        $event = $eventFqcn::reconstitute(
            $eventId,
            $message['payload'],
        );

        return Message::create(
            $event,
            $message['headers'],
        );
    }
}

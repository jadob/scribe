<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Event\EventPayload;
use Jadob\Scribe\Message\Encryption\EventEncryptionProviderInterface;
use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyInterface;
use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyProviderInterface;
use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;
use ReflectionAttribute;
use ReflectionClass;
use Stringable;

/**
 * @template T of EventInterface
 *
 * @implements MessageNormalizerInterface<T>
 *
 * @psalm-import-type MessagePayload from MessageNormalizerInterface
 */
final readonly class EncryptionAwareMessageNormalizer implements MessageNormalizerInterface
{
    public function __construct(
        private EventEncryptionProviderInterface $eventEncryptionProvider,
        private EncryptionKeyProviderInterface $encryptionKeyProvider,
    ) {
    }

    /**
     * @param Message<T> $message
     *
     * @return MessagePayload
     */
    public function normalize(Message $message): array
    {
        $eventPayload = [];
        $event = $message->event;
        $eventReflection = new ReflectionClass($event);

        /** @var EncryptionKeyInterface|null $encryptionKey */
        $encryptionKey = null;

        foreach ($eventReflection->getProperties() as $property) {
            /** @var EventPayload|null $eventPayloadConfig */
            $eventPayloadConfig = null;
            $attributes = $property->getAttributes(EventPayload::class);
            if (count($attributes) > 0) {
                $eventPayloadConfig = $attributes[0]->newInstance();
            }

            $val = $property->getValue($event);
            $key = $property->getName();

            if ($val instanceof Stringable) {
                $val = (string) $val;
            }

            if ($eventPayloadConfig !== null && $eventPayloadConfig->encrypted) {
                if ($encryptionKey === null) {
                    $encryptionKey = $this
                        ->encryptionKeyProvider
                        ->getForAggregate(
                            (string) $message->headers[MessageHeader::AGGREGATE_ID]
                        );
                }

                $eventPayload[$key] = $this
                    ->eventEncryptionProvider
                    ->encrypt(
                        $val,
                        $encryptionKey,
                    );

                continue;
            }

            $eventPayload[$key] = $val;
        }

        return [
            'headers' => $message->headers,
            'payload' => $eventPayload,
        ];
    }

    /**
     * @param MessagePayload  $message
     * @param class-string<T> $eventFqcn
     *
     * @return Message<T>
     */
    public function denormalize(
        array $message,
        string $eventId,
        string $eventFqcn,
    ): Message {
        $eventReflection = new ReflectionClass($eventFqcn);
        foreach ($eventReflection->getProperties() as $property) {
            $attributes = array_filter(
                $property->getAttributes(),
                fn (ReflectionAttribute $attribute) => $attribute->getName() === EventPayload::class
            );

            //  var_dump($attributes);
        }

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

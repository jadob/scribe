<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Normalizer\Normalizer;
use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Event\EventPayload;
use Jadob\Scribe\Message\Encryption\EventEncryptionProviderInterface;
use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyInterface;
use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyProviderInterface;
use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;
use ReflectionClass;

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
        private Normalizer $normalizer,
        private TreeMapper $mapper,
    ) {
    }

    /**
     * @param Message<T> $message
     *
     * @return MessagePayload
     */
    public function normalize(Message $message): array
    {
        /** @var array $normalizedEventPayload */
        $normalizedEventPayload = $this
            ->normalizer
            ->normalize($message);

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

            if ($eventPayloadConfig !== null && $eventPayloadConfig->encrypted) {
                if ($encryptionKey === null) {
                    $encryptionKey = $this
                        ->encryptionKeyProvider
                        ->getForAggregate(
                            (string) $message->headers[MessageHeader::AGGREGATE_ID]
                        );
                }

                $normalizedEventPayload['event'][$key] = $this
                    ->eventEncryptionProvider
                    ->encrypt(
                        $val,
                        $encryptionKey,
                    );
            }
        }

        return $normalizedEventPayload;
    }

    /**
     * @param MessagePayload  $message
     * @param class-string<T> $eventFqcn
     *
     * @return Message<T>
     */
    public function denormalize(
        array $message,
        string $eventFqcn,
    ): Message {
        /** @var EncryptionKeyInterface|null $encryptionKey */
        $encryptionKey = null;

        $eventReflection = new ReflectionClass($eventFqcn);
        foreach ($eventReflection->getProperties() as $property) {
            $attributes = $property->getAttributes(EventPayload::class);

            if (count($attributes) === 0) {
                continue;
            }

            $eventPayloadConfig = $attributes[0]->newInstance();
            $key = $property->getName();
            $value = $message['event'][$key];

            if ($eventPayloadConfig !== null && $eventPayloadConfig->encrypted) {
                if ($encryptionKey === null) {
                    $encryptionKey = $this
                        ->encryptionKeyProvider
                        ->getForAggregate(
                            (string) $message['headers'][MessageHeader::AGGREGATE_ID]
                        );
                }

                $message['event'][$key] = $this
                    ->eventEncryptionProvider
                    ->decrypt(
                        $value,
                        $encryptionKey,
                    );
            }
        }

        $event = $this
            ->mapper
            ->map(
                $eventFqcn,
                $message['event']
            );

        return Message::create(
            $event,
            $message['headers'],
        );
    }
}

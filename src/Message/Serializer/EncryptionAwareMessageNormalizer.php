<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Message\Encryption\EventEncryptionProviderInterface;
use Jadob\Scribe\Message\Message;
use LogicException;
use ReflectionClass;
use Stringable;
use function get_class;
use function is_object;
use function sprintf;

/**
 * @psalm-import-type MessagePayload from MessageNormalizerInterface
 */
final readonly class EncryptionAwareMessageNormalizer implements MessageNormalizerInterface
{
    public function __construct(
        private EventEncryptionProviderInterface $eventEncryptionProvider,
    ) {
    }

    /**
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

            if (is_object($val) && !($val instanceof Stringable)) {
                throw new LogicException(sprintf('class "%s" must implement Stringable in order to be serialized.', get_class($val)));
            }

            $eventPayload[$key] = $val;
        }

        return [
            'headers' => $message->headers,
            'payload' => $eventPayload,
        ];
    }

    public function denormalize(array $message): Message
    {
    }
}

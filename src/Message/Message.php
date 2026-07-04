<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message;

use Jadob\Scribe\Event\EventInterface;

/**
 * @template-covariant T of EventInterface
 */
final readonly class Message
{
    /**
     * @param T                                   $event
     * @param array<non-empty-string, string|int> $headers
     */
    private function __construct(
        private(set) EventInterface $event,
        private(set) array $headers = [],
    ) {
    }

    /**
     * @template E of EventInterface
     *
     * @param E                                   $event
     * @param array<non-empty-string, string|int> $headers
     *
     * @return Message<E>
     */
    public static function create(
        EventInterface $event,
        array $headers = [],
    ): self {
        return new self(
            $event,
            $headers,
        );
    }

    /**
     * @param non-empty-string $key
     *
     * @return Message<T>
     */
    public function withHeader(
        string $key,
        string|int $value,
    ): self {
        $headers = $this->headers;
        $headers[$key] = $value;

        return new self(
            $this->event,
            $headers
        );
    }
}

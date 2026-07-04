<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message;

final readonly class Message
{
    /**
     * @param array<non-empty-string, string|int> $headers
     */
    private function __construct(
        private(set) object $event,
        private(set) array $headers = [],
    ) {
    }

    public static function create(
        object $event,
        array $headers = []
    ): self {
        return new self(
            $event,
            $headers
        );
    }

    /**
     * @param non-empty-string $key
     */
    public function withHeader(
        string $key,
        string|int $value
    ): self {
        $headers = $this->headers;
        $headers[$key] = $value;

        return new self(
            $this->event,
            $headers
        );
    }
}

<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption\Key;

final readonly class StringEncryptionKey implements EncryptionKeyInterface
{
    public function __construct(
        private string $key,
    ) {
    }

    public function get(): string
    {
        return $this->key;
    }
}

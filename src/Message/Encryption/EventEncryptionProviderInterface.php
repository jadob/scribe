<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption;

use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyInterface;

interface EventEncryptionProviderInterface
{
    public function encrypt(
        mixed $payload,
        EncryptionKeyInterface $key,
    ): string;

    public function decrypt(
        string $encryptedPayload,
        EncryptionKeyInterface $key,
    ): mixed;
}

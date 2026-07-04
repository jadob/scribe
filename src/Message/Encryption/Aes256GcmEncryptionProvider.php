<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption;

use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyInterface;
use JsonException;
use LogicException;

use function base64_decode;
use function base64_encode;
use function explode;
use function is_string;
use function json_encode;
use function openssl_cipher_iv_length;
use function openssl_decrypt;
use function openssl_encrypt;
use function random_bytes;
use function sprintf;

use const JSON_THROW_ON_ERROR;

final readonly class Aes256GcmEncryptionProvider implements EventEncryptionProviderInterface
{
    private const string CIPHER = 'aes-256-gcm';

    public function encrypt(
        mixed $payload,
        EncryptionKeyInterface $key,
    ): string {
        $encryptionKey = $key->get();
        $this->assertKeyIsString($encryptionKey);

        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));

        $ciphertext = openssl_encrypt(
            $this->normalizePayload($payload),
            self::CIPHER,
            $key->get(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return sprintf(
            '%s|%s|%s',
            base64_encode($iv),
            base64_encode($tag),
            base64_encode($ciphertext)
        );
    }

    public function decrypt(
        string $encryptedPayload,
        EncryptionKeyInterface $key
    ): string {
        $encryptionKey = $key->get();
        $this->assertKeyIsString($encryptionKey);

        /** @var array<int,non-empty-string> $explodedValue */
        $explodedValue = explode('|', $encryptedPayload);

        $iv = base64_decode($explodedValue[0], true);
        $tag = base64_decode($explodedValue[1], true);
        $ciphertext = base64_decode($explodedValue[2], true);

        return openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );
    }

    /**
     * @throws JsonException
     */
    private function normalizePayload(
        mixed $payload
    ): string {
        return json_encode(
            $payload,
            JSON_THROW_ON_ERROR
        );
    }

    private function assertKeyIsString(
        mixed $value
    ): void {
        if (is_string($value) === false) {
            throw new LogicException('Encryption key must be a string');
        }
    }
}

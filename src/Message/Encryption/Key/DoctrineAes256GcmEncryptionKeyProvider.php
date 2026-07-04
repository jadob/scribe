<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption\Key;

final readonly class DoctrineAes256GcmEncryptionKeyProvider implements EncryptionKeyProviderInterface
{
    public function getForAggregate(string $aggregateRootId): EncryptionKeyInterface
    {

    }
}

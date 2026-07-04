<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption\Key;

interface EncryptionKeyProviderInterface
{
    public function getForAggregate(
        string $aggregateRootId
    ): EncryptionKeyInterface;
}

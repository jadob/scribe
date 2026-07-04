<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption\Key;

use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;

interface EncryptionKeyProviderInterface
{
    public function getForAggregate(
        AggregateRootIdInterface $aggregateRootId
    ): EncryptionKeyInterface;
}

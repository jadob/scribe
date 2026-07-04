<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption\Key;

interface EncryptionKeyInterface
{
    public function get(): mixed;
}

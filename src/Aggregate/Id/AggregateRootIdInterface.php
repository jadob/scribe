<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate\Id;

use Stringable;

interface AggregateRootIdInterface extends Stringable
{
    public static function fromString(string $id): self;
}

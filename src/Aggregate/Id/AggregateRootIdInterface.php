<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate\Id;

interface AggregateRootIdInterface
{
    public function toString(): string;

    public static function fromString(string $id): self;
}

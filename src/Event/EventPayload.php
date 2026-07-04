<?php

declare(strict_types=1);

namespace Jadob\Scribe\Event;

use Attribute;

/**
 * This attribute is optional.
 * If you leave these values default, it will be serialized as-is.
 * If you enable encryption, an encryption key will be generated, persisted to separate table and encrypted value will
 * be stored here. However, it is up to message serializer if this will happen.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class EventPayload
{
    /**
     * @param string|null $unserializeMethod used to restore the object from persistence
     */
    public function __construct(
        private(set) ?string $key = null,
        private bool $encrypted = false,
        private(set) ?string $unserializeMethod = null,
    ) {
    }
}

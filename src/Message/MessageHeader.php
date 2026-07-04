<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message;

final readonly class MessageHeader
{
    public const string AGGREGATE_ID = '_aggregate_id';
    public const string AGGREGATE_REVISION = '_revision_id';
    public const string AGGREGATE_TYPE = '_aggregate_type';
    public const string EVENT_TYPE = '_event_type';
    public const string RECORDED_AT = '_event_recorded_at';
}

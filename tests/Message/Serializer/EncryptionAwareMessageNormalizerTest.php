<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Aggregate\Id\UuidAggregateId;
use Jadob\Scribe\Event\Id\UuidEventId;
use Jadob\Scribe\Fixtures\Event\UserFavoriteFoodAddedEvent;
use Jadob\Scribe\Message\Encryption\EventEncryptionProviderInterface;
use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncryptionAwareMessageNormalizerTest extends TestCase
{
    private EncryptionAwareMessageNormalizer $normalizer;
    private EventEncryptionProviderInterface&MockObject $eventEncryptionProviderMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventEncryptionProviderMock = $this->createMock(EventEncryptionProviderInterface::class);

        $this->normalizer = new EncryptionAwareMessageNormalizer(
            $this->eventEncryptionProviderMock,
        );
    }

    public function testEventSerializationWithoutEncryptedProperties(): void
    {
        $this
            ->eventEncryptionProviderMock
            ->expects($this->never())
            ->method('encrypt');

        $result = $this
            ->normalizer
            ->normalize(
                Message::create(
                    new UserFavoriteFoodAddedEvent(
                        eventId: UuidEventId::fromString('019f2d1f-3eec-7f6f-8865-2af1f6a0d5ee'),
                        userId: UuidAggregateId::fromString('019f2d1c-f74c-7611-b8e3-4bffb97a12f2'),
                        favoriteFoodName: 'spaghetti'
                    )
                )
                    ->withHeader(MessageHeader::AGGREGATE_REVISION, 1)
            );

        self::assertEquals(
            [
                'headers' => [
                    '_revision_id' => 1,
                ],
                'payload' => [
                    'eventId' => '019f2d1f-3eec-7f6f-8865-2af1f6a0d5ee',
                    'userId' => '019f2d1c-f74c-7611-b8e3-4bffb97a12f2',
                    'favoriteFoodName' => 'spaghetti'
                ],
            ],
            $result
        );
    }

//    public function _testEventDeserializationWithoutEncryptedProperties(): void
//    {
//        $this
//            ->eventEncryptionProviderMock
//            ->expects($this->never())
//            ->method('encrypt');
//
//        var_dump(
//            $this
//            ->serializer
//            ->denormalize(
//                [
//                    'headers' => [
//                        '_revision_id' => 1,
//                    ],
//                    'payload' => [
//                        'eventId' => '019f2d1f-3eec-7f6f-8865-2af1f6a0d5ee',
//                        'userId' => '019f2d1c-f74c-7611-b8e3-4bffb97a12f2',
//                        'favoriteFoodName' => 'spaghetti'
//                    ],
//                ]
//            )
//        );
//
//
//
//            ,
//            $result
//        );
//    }
}

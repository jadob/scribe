<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Serializer;

use Jadob\Scribe\Aggregate\Id\UuidAggregateId;
use Jadob\Scribe\Event\EventInterface;
use Jadob\Scribe\Event\Id\UuidEventId;
use Jadob\Scribe\Fixtures\Event\UserCreatedEvent;
use Jadob\Scribe\Fixtures\Event\UserFavoriteFoodAddedEvent;
use Jadob\Scribe\Message\Encryption\EventEncryptionProviderInterface;
use Jadob\Scribe\Message\Encryption\Key\EncryptionKeyProviderInterface;
use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EncryptionAwareMessageNormalizerTest extends TestCase
{
    /** @var EncryptionAwareMessageNormalizer<EventInterface> */
    private EncryptionAwareMessageNormalizer $normalizer;
    private EventEncryptionProviderInterface&MockObject $eventEncryptionProviderMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventEncryptionProviderMock = $this->createMock(EventEncryptionProviderInterface::class);

        $this->normalizer = new EncryptionAwareMessageNormalizer(
            $this->eventEncryptionProviderMock,
            $this->createStub(EncryptionKeyProviderInterface::class),
        );
    }

    public function testEventNormalizationWithoutEncryptedProperties(): void
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
                    ),
                    [
                        MessageHeader::AGGREGATE_REVISION => 1,
                    ]
                )
            );

        self::assertSame(
            [
                'headers' => [
                    '_revision_id' => 1,
                ],
                'payload' => [
                    'eventId' => '019f2d1f-3eec-7f6f-8865-2af1f6a0d5ee',
                    'userId' => '019f2d1c-f74c-7611-b8e3-4bffb97a12f2',
                    'favoriteFoodName' => 'spaghetti',
                ],
            ],
            $result
        );
    }

    public function testEventNormalizationWithEncryptedProperties(): void
    {
        $this
            ->eventEncryptionProviderMock
            ->expects($this->once())
            ->method('encrypt')
            ->willReturnCallback(
                function (string $prop) {
                    return sprintf('ENCRYPTED_%s', md5($prop));
                }
            );

        $result = $this
            ->normalizer
            ->normalize(
                Message::create(
                    new UserCreatedEvent(
                        userId: UuidAggregateId::fromString('019f2d1c-f74c-7611-b8e3-4bffb97a12f2'),
                        username: 'jdoe',
                        email: 'j.doe1999@example.com'
                    ),
                    [
                        MessageHeader::AGGREGATE_ID => '019f2d8c-7285-7aa0-99b3-3a1d672cf0eb',
                    ]
                )
            );

        self::assertSame(
            [
                'headers' => [
                    '_aggregate_id' => '019f2d8c-7285-7aa0-99b3-3a1d672cf0eb',
                ],
                'payload' => [
                    'userId' => '019f2d1c-f74c-7611-b8e3-4bffb97a12f2',
                    'username' => 'jdoe',
                    'email' => 'ENCRYPTED_fd9eceba31d0de547a6410a5e63f4851',
                ],
            ],
            $result
        );
    }

    public function testEventDenormalizationWithoutEncryptedProperties(): void
    {
        $this
            ->eventEncryptionProviderMock
            ->expects($this->never())
            ->method('encrypt');

        /** @var Message<UserFavoriteFoodAddedEvent> $message */
        $message = $this
            ->normalizer
            ->denormalize(
                [
                    'headers' => [
                        '_revision_id' => 1,
                    ],
                    'payload' => [
                        'eventId' => '019f2d3f-8e6c-753e-a95d-35803489f527',
                        'userId' => '019f2d3f-b86d-79f4-97ed-5611e717f94d',
                        'favoriteFoodName' => 'gabagool',
                    ],
                ],
                '019f2d3f-8e6c-753e-a95d-35803489f527',
                UserFavoriteFoodAddedEvent::class
            );

        self::assertInstanceOf(UserFavoriteFoodAddedEvent::class, $message->event);
        self::assertSame(1, $message->headers[MessageHeader::AGGREGATE_REVISION]);
        self::assertSame('gabagool', $message->event->favoriteFoodName);
    }
}

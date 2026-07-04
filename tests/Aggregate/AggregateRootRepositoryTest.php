<?php

declare(strict_types=1);

namespace Jadob\Scribe\Aggregate;

use Jadob\Scribe\Aggregate\Exception\NoEventsRecordedException;
use Jadob\Scribe\Aggregate\Exception\UnsupportedAggregateTypeException;
use Jadob\Scribe\Aggregate\Id\UuidAggregateId;
use Jadob\Scribe\Aggregate\Id\UuidBinaryAggregateId;
use Jadob\Scribe\Event\MessageRepositoryInterface;
use Jadob\Scribe\Fixtures\Aggregate\Order;
use Jadob\Scribe\Fixtures\Aggregate\User;
use Jadob\Scribe\Fixtures\Event\UserCreatedEvent;
use Jadob\Scribe\Message\Message;
use Jadob\Scribe\Message\MessageHeader;
use PHPUnit\Framework\TestCase;

class AggregateRootRepositoryTest extends TestCase
{
    public function testRepositoryWillPreventSavingAggregateNotMeantToBeUsedForHisType(): void
    {
        $repository = new AggregateRootRepository(
            Order::class,
            $this->createStub(MessageRepositoryInterface::class)
        );

        $this->expectException(UnsupportedAggregateTypeException::class);
        $this->expectExceptionMessage(
            'Invalid aggregate type (received "Jadob\Scribe\Fixtures\Aggregate\User", repository configured for "Jadob\Scribe\Fixtures\Aggregate\Order")'
        );

        $repository->store(User::create(
            UuidBinaryAggregateId::new7(),
            'jdoe',
            'jdoe@example.com',
        ));
    }

    public function testRepositoryWillPreventSavingAggregateWithoutEvents(): void
    {
        $repository = new AggregateRootRepository(
            User::class,
            $this->createStub(MessageRepositoryInterface::class)
        );

        $this->expectException(UnsupportedAggregateTypeException::class);
        $this->expectExceptionMessage(
            'Invalid aggregate type (received "Jadob\Scribe\Fixtures\Aggregate\User", repository configured for "Jadob\Scribe\Fixtures\Aggregate\Order")'
        );

        $user = User::create(
            UuidBinaryAggregateId::new7(),
            'jdoe',
            'jdoe@example.com'
        );

        $user->popEvents();

        $this->expectException(NoEventsRecordedException::class);
        $this->expectExceptionMessage('No events found for aggregate Jadob\Scribe\Fixtures\Aggregate\User');

        $repository->store($user);
    }

    public function testRepositoryWrapEventsIntoMessageWithMetadata(): void
    {
        $repository = new AggregateRootRepository(
            User::class,
            $messageRepositoryMock = $this->createMock(MessageRepositoryInterface::class)
        );

        $aggregateId = UuidAggregateId::new7();
        $user = User::create(
            $aggregateId,
            'jdoe',
            'jdoe@example.com'
        );

        $messageRepositoryMock
            ->expects($this->once())
            ->method('store')
            ->willReturnCallback(
                function (Message ...$messages) use ($aggregateId): void {
                    self::assertCount(1, $messages);

                    $message = $messages[0];
                    self::assertInstanceOf(UserCreatedEvent::class, $message->event);
                    self::assertSame($aggregateId->toString(), $message->headers[MessageHeader::AGGREGATE_ID]);
                    self::assertSame(1, $message->headers[MessageHeader::AGGREGATE_REVISION]);
                    self::assertSame(User::class, $message->headers[MessageHeader::AGGREGATE_TYPE]);
                }
            );

        $repository->store($user);
    }
}

<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message;

use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Jadob\Scribe\Aggregate\Id\AggregateRootIdInterface;
use Jadob\Scribe\Doctrine\DbalConnectionService;
use Jadob\Scribe\Message\Serializer\MessageSerializerInterface;
use Ramsey\Uuid\Uuid;

final readonly class DoctrineDbalMessageRepository implements MessageRepositoryInterface
{
    private const string EVENT_STORE_TABLE_NAME = 'event_store';

    public function __construct(
        private MessageSerializerInterface $messageSerializer,
        private Connection $connection,
        private DbalConnectionService $connectionService,
    ) {
    }

    public function load(AggregateRootIdInterface $aggregateRootId): array
    {
        $this->ensureTableExists();

        /** @var array{
         *     id: non-empty-string,
         *     aggregate_id: non-empty-string,
         *     aggregate_type: class-string,
         *     aggregate_revision: int,
         *     recorded_at: non-empty-string,
         *     payload: non-empty-string
         * }[] $rawMessages
         */
        $rawMessages = $this
            ->connection
            ->createQueryBuilder()
            ->select('*')
            ->from(self::EVENT_STORE_TABLE_NAME, 'es')
            ->where('es.aggregate_id = :id')
            ->orderBy('es.aggregate_revision', 'ASC')
            ->setParameter('id', Uuid::fromString((string) $aggregateRootId)->getBytes())
            ->fetchAllAssociative();

        $output = [];
        foreach ($rawMessages as $rawMessage) {
            $output[] = $this
                ->messageSerializer
                ->deserialize($rawMessage['payload']);
        }

        return $output;
    }

    public function store(Message ...$messages): void
    {
        $this->ensureTableExists();

        foreach ($messages as $message) {
            $serializedMessage = $this
                ->messageSerializer
                ->serialize($message);

            $aggregateId = $message->headers[MessageHeader::AGGREGATE_ID];
            $aggregateType = $message->headers[MessageHeader::AGGREGATE_TYPE];
            $eventVersion = $message->headers[MessageHeader::AGGREGATE_REVISION];
            $recordedAt = $message->headers[MessageHeader::RECORDED_AT];

            $this
                ->connection
                ->insert(
                    self::EVENT_STORE_TABLE_NAME,
                    [
                        'id' => Uuid::uuid7()->getBytes(),
                        'aggregate_id' => Uuid::fromString($aggregateId)->getBytes(),
                        'aggregate_revision' => $eventVersion,
                        'aggregate_type' => $aggregateType,
                        'recorded_at' => DateTimeImmutable::createFromTimestamp($recordedAt)->format('Y-m-d H:i:s'),
                        'payload' => $serializedMessage,
                    ]
                );
        }
    }

    public function ensureTableExists(): void
    {
        $exists = $this
            ->connectionService
            ->tableExists(self::EVENT_STORE_TABLE_NAME);

        if ($exists) {
            return;
        }

        $table = new Table(
            self::EVENT_STORE_TABLE_NAME,
            columns: [
                Column::editor()
                    ->setName(UnqualifiedName::unquoted('id'))
                    ->setType(Type::getType(Types::BINARY))
                    ->setLength(16)
                    ->create(),
                Column::editor()
                    ->setName(UnqualifiedName::unquoted('aggregate_id'))
                    ->setType(Type::getType(Types::BINARY))
                    ->setLength(16)
                    ->create(),
                Column::editor()
                    ->setName(UnqualifiedName::unquoted('aggregate_revision'))
                    ->setType(Type::getType(Types::INTEGER))
                    ->create(),
                Column::editor()
                    ->setName(UnqualifiedName::unquoted('aggregate_type'))
                    ->setType(Type::getType(Types::TEXT))
                    ->setLength(256)
                    ->create(),
                Column::editor()
                    ->setName(UnqualifiedName::unquoted('recorded_at'))
                    ->setType(Type::getType(Types::DATE_IMMUTABLE))
                    ->setLength(256)
                    ->create(),
                Column::editor()
                    ->setName(UnqualifiedName::unquoted('payload'))
                    ->setType(Type::getType(Types::TEXT))
                    ->setLength(65535)
                    ->create(),
            ],
            indexes: [
                new Index(
                    'event_store_pk',
                    ['id'],
                    isPrimary: true
                ),
            ]
        );

        $this
            ->connection
            ->createSchemaManager()
            ->createTable($table);
    }
}

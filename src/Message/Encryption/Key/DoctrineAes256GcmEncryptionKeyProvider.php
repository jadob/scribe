<?php

declare(strict_types=1);

namespace Jadob\Scribe\Message\Encryption\Key;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\Name\UnqualifiedName;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Ramsey\Uuid\Uuid;

final readonly class DoctrineAes256GcmEncryptionKeyProvider implements EncryptionKeyProviderInterface
{
    private const string ENCRYPTION_KEYS_TABLE = 'event_store_encryption_keys';

    public function __construct(
        private Connection $connection,
    ) {
    }

    public function getForAggregate(string $aggregateRootId): EncryptionKeyInterface
    {
        $this->ensureSchemaExists();

        $uuid = Uuid::fromString($aggregateRootId);

        $existing = $this
            ->connection
            ->createQueryBuilder()
            ->select('encryption_key')
            ->from(self::ENCRYPTION_KEYS_TABLE)
            ->where('aggregate_id = :aggregate_id')
            ->setParameter('aggregate_id', $uuid->getBytes())
            ->fetchOne();

    }

    private function ensureSchemaExists(): void
    {
        $schemaManager = $this->connection->createSchemaManager();
        $exists = $schemaManager
            ->tablesExist([
                self::ENCRYPTION_KEYS_TABLE,
            ]);

        if ($exists) {
            return;
        }

        $schema = new Table(
            self::ENCRYPTION_KEYS_TABLE,
            columns: [
                Column::editor()
                    ->setName(UnqualifiedName::quoted('aggregate_id'))
                    ->setLength(16)
                    ->setType(Type::getType(Types::BINARY))
                    ->setNotNull(true)
                    ->create(),
                Column::editor()
                    ->setName(UnqualifiedName::quoted('encryption_key'))
                    ->setLength(16)
                    ->setType(Type::getType(Types::TEXT))
                    ->setNotNull(true)
                    ->create(),
            ],
            indexes: [
                new Index(
                    'scribe_encryption_keys_pk',
                    [
                        'aggregate_id',
                    ],
                    isPrimary: true
                ),
            ]
        );

        $schemaManager->createTable($schema);
    }
}

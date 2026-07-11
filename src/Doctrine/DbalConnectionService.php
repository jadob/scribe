<?php

declare(strict_types=1);

namespace Jadob\Scribe\Doctrine;

use Doctrine\DBAL\Connection;

final readonly class DbalConnectionService
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function tableExists(
        string $table
    ): bool {
        $databaseName = $this
            ->connection
            ->getDatabase();

        if ($databaseName === null) {
            throw new LogicException('DBAL connection does not contain information about database name.');
        }

        /** @var int $result */
        $result = $this
            ->connection
            ->fetchOne(
                '
                SELECT COUNT(*) as count
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = :db_name
                  AND TABLE_NAME = :table_name',
                [
                    'db_name' => $databaseName,
                    'table_name' => $table,
                ]
            );

        return (bool) $result;
    }
}

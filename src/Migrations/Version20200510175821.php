<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200510175821 extends AbstractMigration
{
    private string $table = 'payment_session';

    public function getDescription(): string
    {
        return 'Создаем таблицу для хранения платежных сессий';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable($this->table)) {
            $table = $schema->createTable($this->table);

            $table
                ->addColumn('uuid', Types::GUID)
                ->setNotnull(true);

            $table->setPrimaryKey(['uuid',]);
            $table->addUniqueIndex(['uuid',], 'payment_session_uuid_index');

            $table
                ->addColumn('amount', Types::JSON)
                ->setNotnull(true);

            $table
                ->addColumn('purpose', Types::STRING)
                ->setNotnull(true);

            $table
                ->addColumn('confirmation_url', Types::STRING)
                ->setNotnull(false);

            $table
                ->addColumn('created_at', Types::DATETIME_IMMUTABLE)
                ->setNotnull(true);

            $table
                ->addColumn('expire_at', Types::DATETIME_IMMUTABLE)
                ->setNotnull(true);
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable($this->table)) {
            $schema->dropTable($this->table);
        }
    }
}

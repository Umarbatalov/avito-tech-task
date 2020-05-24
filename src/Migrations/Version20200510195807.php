<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200510195807 extends AbstractMigration
{
    private string $table = 'payment';

    public function getDescription(): string
    {
        return 'Создаем таблицу платежей';
    }

    public function up(Schema $schema): void
    {
        if (!$schema->hasTable($this->table)) {
            $table = $schema->createTable($this->table);

            $table->addColumn(
                'id',
                Types::INTEGER,
                ['autoincrement' => true,]
            );

            $table->setPrimaryKey(['id',]);
            $table->addUniqueIndex(['id',], 'payment_id_index');

            $table
                ->addColumn('session_uuid', Types::GUID)
                ->setNotnull(true);

            $table->addForeignKeyConstraint(
                'payment_session',
                ['session_uuid',],
                ['uuid',]
            );

            $table->addUniqueIndex(['session_uuid',], 'payment_session_uuid_index');

            $table
                ->addColumn('amount', Types::JSON)
                ->setNotnull(true);

            $table
                ->addColumn('purpose', Types::STRING)
                ->setNotnull(true);

            $table
                ->addColumn('created_at', Types::DATETIME_IMMUTABLE)
                ->setNotnull(true);

            $table->addIndex(['created_at',], 'payment_created_at_index');
        }
    }

    public function down(Schema $schema): void
    {
        if ($schema->hasTable($this->table)) {
            $schema->dropTable($this->table);
        }
    }
}

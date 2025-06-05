<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250605090259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename transaction table to transactions if needed';
    }

    public function up(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        if ($sm->tablesExist(['transaction'])) {
            $this->addSql('ALTER TABLE transaction RENAME TO transactions');
        }
    }

    public function down(Schema $schema): void
    {
        $sm = $this->connection->createSchemaManager();
        if ($sm->tablesExist(['transactions'])) {
            $this->addSql('ALTER TABLE transactions RENAME TO transaction');
        }
    }
}


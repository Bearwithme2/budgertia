<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202501010000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration creating transactions table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE transactions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, amount INTEGER NOT NULL, description VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE transactions');
    }
}


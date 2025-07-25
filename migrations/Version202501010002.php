<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202501010002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles column to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE user ADD COLUMN roles CLOB NOT NULL DEFAULT '[]'");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, email, password FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO user (id, email, password) SELECT id, email, password FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}

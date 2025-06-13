<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202501010003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add level and is_read columns to notification table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("ALTER TABLE notification ADD COLUMN level VARCHAR(50) NOT NULL DEFAULT 'info'");
        $this->addSql('ALTER TABLE notification ADD COLUMN is_read BOOLEAN NOT NULL DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE TEMPORARY TABLE __temp__notification AS SELECT id, user_id, message, created_at FROM notification');
        $this->addSql('DROP TABLE notification');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, message VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO notification (id, user_id, message, created_at) SELECT id, user_id, message, created_at FROM __temp__notification');
        $this->addSql('DROP TABLE __temp__notification');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
    }
}

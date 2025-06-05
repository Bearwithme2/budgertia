<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version202501010001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create domain tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS transactions');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('CREATE TABLE category (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE TABLE transactions (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, category_id INTEGER DEFAULT NULL, amount INTEGER NOT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, CONSTRAINT FK_723705D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_723705D112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_723705D1A76ED395 ON transactions (user_id)');
        $this->addSql('CREATE INDEX IDX_723705D112469DE2 ON transactions (category_id)');
        $this->addSql('CREATE TABLE budget_limit (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, user_id INTEGER NOT NULL, amount INTEGER NOT NULL, CONSTRAINT FK_7E13306B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7E13306BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7E13306B12469DE2 ON budget_limit (category_id)');
        $this->addSql('CREATE INDEX IDX_7E13306BA76ED395 ON budget_limit (user_id)');
        $this->addSql('CREATE TABLE savings_goal (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, target_amount INTEGER NOT NULL, current_amount INTEGER NOT NULL, CONSTRAINT FK_44F683F6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_44F683F6A76ED395 ON savings_goal (user_id)');
        $this->addSql('CREATE TABLE notification (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, message VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE transactions');
        $this->addSql('DROP TABLE budget_limit');
        $this->addSql('DROP TABLE savings_goal');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE user');
    }
}

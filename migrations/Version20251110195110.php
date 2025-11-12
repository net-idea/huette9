<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251110195110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create contact table for storing contact form submissions';
    }

    public function up(Schema $schema): void
    {
        // Run platform-specific SQL so the migration works with SQLite (dev) and MySQL/MariaDB (prod)
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            // SQLite does not support COMMENT, ENGINE or AUTO_INCREMENT syntax used by MySQL
            $this->addSql('CREATE TABLE contact (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, message CLOB NOT NULL, created_at DATETIME NOT NULL)');
        } else {
            // MySQL / MariaDB
            $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'' . '(DC2Type:datetime_immutable)\'' . ', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE contact');
    }
}

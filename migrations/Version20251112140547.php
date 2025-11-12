<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251112140547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add form_contact, form_submission_meta and messenger_messages (platform-aware); keep contact table intact on MySQL/MariaDB.';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            // SQLite variants
            $this->addSql('CREATE TABLE form_submission_meta (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, ip VARCHAR(64) DEFAULT NULL, user_agent VARCHAR(400) DEFAULT NULL, time VARCHAR(40) DEFAULT NULL, host VARCHAR(200) DEFAULT NULL)');
            $this->addSql('CREATE TABLE form_contact (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, meta_id INTEGER DEFAULT NULL, name VARCHAR(160) NOT NULL, email_address VARCHAR(200) NOT NULL, phone VARCHAR(40) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, consent BOOLEAN NOT NULL, message CLOB NOT NULL, copy BOOLEAN NOT NULL, created_at DATETIME NOT NULL, CONSTRAINT FK_7D0E860339FCA6F9 FOREIGN KEY (meta_id) REFERENCES form_submission_meta (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE)');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_7D0E860339FCA6F9 ON form_contact (meta_id)');
            $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
            $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        } else {
            // MySQL / MariaDB variants
            $this->addSql("CREATE TABLE form_submission_meta (id INT AUTO_INCREMENT NOT NULL, ip VARCHAR(64) DEFAULT NULL, user_agent VARCHAR(400) DEFAULT NULL, time VARCHAR(40) DEFAULT NULL, host VARCHAR(200) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            $this->addSql("CREATE TABLE form_contact (id INT AUTO_INCREMENT NOT NULL, meta_id INT DEFAULT NULL, name VARCHAR(160) NOT NULL, email_address VARCHAR(200) NOT NULL, phone VARCHAR(40) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, consent TINYINT(1) NOT NULL, message LONGTEXT NOT NULL, copy TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX UNIQ_7D0E860339FCA6F9 (meta_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
            $this->addSql('ALTER TABLE form_contact ADD CONSTRAINT FK_7D0E860339FCA6F9 FOREIGN KEY (meta_id) REFERENCES form_submission_meta (id) ON DELETE SET NULL');
            $this->addSql("CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ($platform === 'sqlite') {
            // Reverse SQLite changes
            $this->addSql('DROP TABLE messenger_messages');
            $this->addSql('DROP TABLE form_contact');
            $this->addSql('DROP TABLE form_submission_meta');
        } else {
            // Reverse MySQL changes
            $this->addSql('DROP TABLE messenger_messages');
            $this->addSql('ALTER TABLE form_contact DROP FOREIGN KEY FK_7D0E860339FCA6F9');
            $this->addSql('DROP TABLE form_contact');
            $this->addSql('DROP TABLE form_submission_meta');
        }
    }
}

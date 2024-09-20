<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920000415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, payer_id INT DEFAULT NULL, lastname VARCHAR(50) NOT NULL, firstname VARCHAR(50) NOT NULL, middlename VARCHAR(30) DEFAULT \'\' NOT NULL, alias VARCHAR(10) NOT NULL, email VARCHAR(60) NOT NULL, fee SMALLINT UNSIGNED NOT NULL, notes VARCHAR(400) NOT NULL, phone VARCHAR(12) NOT NULL, address VARCHAR(100) NOT NULL, secondary_address VARCHAR(50) NOT NULL, city VARCHAR(40) NOT NULL, state VARCHAR(2) NOT NULL, postal_code VARCHAR(10) NOT NULL, active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_34DCD176C17AD9A9 (payer_id), UNIQUE INDEX unique_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176C17AD9A9 FOREIGN KEY (payer_id) REFERENCES person (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176C17AD9A9');
        $this->addSql('DROP TABLE person');
    }
}

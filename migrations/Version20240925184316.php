<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925184316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, payer_id INT NOT NULL, date DATE NOT NULL, internal_notes VARCHAR(200) NOT NULL, INDEX IDX_90651744C17AD9A9 (payer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_90651744C17AD9A9 FOREIGN KEY (payer_id) REFERENCES person (id)');
        //$this->addSql('ALTER TABLE person CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE service ADD invoice_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD22989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD22989F1FD ON service (invoice_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD22989F1FD');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_90651744C17AD9A9');
        $this->addSql('DROP TABLE invoice');
        //$this->addSql('ALTER TABLE person CHANGE type type VARCHAR(255) DEFAULT \'patient\' NOT NULL');
        $this->addSql('DROP INDEX IDX_E19D9AD22989F1FD ON service');
        $this->addSql('ALTER TABLE service DROP invoice_id');
    }
}

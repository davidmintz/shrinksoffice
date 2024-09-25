<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240925020135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE service ADD payer_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2C17AD9A9 FOREIGN KEY (payer_id) REFERENCES person (id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD2C17AD9A9 ON service (payer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2C17AD9A9');
        $this->addSql('DROP INDEX IDX_E19D9AD2C17AD9A9 ON service');
        $this->addSql('ALTER TABLE service DROP payer_id');
    }
}

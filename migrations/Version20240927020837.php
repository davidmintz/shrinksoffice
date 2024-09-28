<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240927020837 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE credit (id INT AUTO_INCREMENT NOT NULL, payer_id INT NOT NULL, date DATE NOT NULL, notes VARCHAR(255) NOT NULL, amount SMALLINT UNSIGNED NOT NULL, INDEX IDX_1CC16EFEC17AD9A9 (payer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE credit_invoice (credit_id INT NOT NULL, invoice_id INT NOT NULL, INDEX IDX_AAE1706BCE062FF9 (credit_id), INDEX IDX_AAE1706B2989F1FD (invoice_id), PRIMARY KEY(credit_id, invoice_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEC17AD9A9 FOREIGN KEY (payer_id) REFERENCES person (id)');
        $this->addSql('ALTER TABLE credit_invoice ADD CONSTRAINT FK_AAE1706BCE062FF9 FOREIGN KEY (credit_id) REFERENCES credit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE credit_invoice ADD CONSTRAINT FK_AAE1706B2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE credit DROP FOREIGN KEY FK_1CC16EFEC17AD9A9');
        $this->addSql('ALTER TABLE credit_invoice DROP FOREIGN KEY FK_AAE1706BCE062FF9');
        $this->addSql('ALTER TABLE credit_invoice DROP FOREIGN KEY FK_AAE1706B2989F1FD');
        $this->addSql('DROP TABLE credit');
        $this->addSql('DROP TABLE credit_invoice');

    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240901193252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create Person entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE person (id INT AUTO_INCREMENT NOT NULL, payer_id INT DEFAULT NULL, lastname VARCHAR(35) NOT NULL, firstname VARCHAR(35) NOT NULL, middlename VARCHAR(25) NOT NULL, active TINYINT(1) NOT NULL, address1 VARCHAR(35) NOT NULL, address2 VARCHAR(35) NOT NULL, state VARCHAR(2) NOT NULL, postal_code VARCHAR(10) NOT NULL, email VARCHAR(50) NOT NULL, phone VARCHAR(10) NOT NULL, fee INT NOT NULL, INDEX IDX_34DCD176C17AD9A9 (payer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        // I didn't ask for this,,,
        //$this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176C17AD9A9 FOREIGN KEY (payer_id) REFERENCES person (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE person DROP FOREIGN KEY FK_34DCD176C17AD9A9');
        $this->addSql('DROP TABLE person');
        //$this->addSql('DROP TABLE messenger_messages');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240903024249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add unique email constraint to person';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(' ALTER TABLE person ADD constraint unique_email unique(email)');

    }

    public function down(Schema $schema): void
    {
       $this->addSql('ALTER TABLE person DROP INDEX unique_email');

    }
}

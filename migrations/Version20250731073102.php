<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250731073102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add slug and timestamps to starship';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship ADD created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE starship ADD updated_at DATETIME DEFAULT NULL');
        // Comments for Doctrine type are not needed for MySQL
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship DROP slug, DROP updated_at, DROP created_at, CHANGE arrived_at arrived_at DATETIME NOT NULL');
    }
}

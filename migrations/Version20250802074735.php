<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250802074735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship_droid DROP CONSTRAINT FK_1C7FBE889B24DF5');
        $this->addSql('ALTER TABLE starship_droid DROP CONSTRAINT FK_1C7FBE88AB064EF');
        $this->addSql('ALTER TABLE starship_droid DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE starship_droid ADD id INT AUTO_INCREMENT NOT NULL PRIMARY KEY');
        $this->addSql('ALTER TABLE starship_droid ADD assigned_at DATETIME DEFAULT NOW() NOT NULL');
        $this->addSql('ALTER TABLE starship_droid ADD CONSTRAINT FK_1C7FBE889B24DF5 FOREIGN KEY (starship_id) REFERENCES starship (id)');
        $this->addSql('ALTER TABLE starship_droid ADD CONSTRAINT FK_1C7FBE88AB064EF FOREIGN KEY (droid_id) REFERENCES droid (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship_droid DROP FOREIGN KEY FK_1C7FBE889B24DF5');
        $this->addSql('ALTER TABLE starship_droid DROP FOREIGN KEY FK_1C7FBE88AB064EF');
        $this->addSql('DROP TABLE starship_droid');
    }
}

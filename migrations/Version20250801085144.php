<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801085144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship_part ADD relation_id INT NOT NULL');
        $this->addSql('ALTER TABLE starship_part ADD CONSTRAINT FK_41C447373256915B FOREIGN KEY (relation_id) REFERENCES starship (id)');
        $this->addSql('CREATE INDEX IDX_41C447373256915B ON starship_part (relation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE starship_part DROP FOREIGN KEY FK_41C447373256915B');
        $this->addSql('DROP INDEX IDX_41C447373256915B ON starship_part');
        $this->addSql('ALTER TABLE starship_part DROP relation_id');
    }
}

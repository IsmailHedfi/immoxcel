<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240219202819 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expenses ADD materials_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B3A9FC940 FOREIGN KEY (materials_id) REFERENCES materials (id)');
        $this->addSql('CREATE INDEX IDX_2496F35B3A9FC940 ON expenses (materials_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B3A9FC940');
        $this->addSql('DROP INDEX IDX_2496F35B3A9FC940 ON expenses');
        $this->addSql('ALTER TABLE expenses DROP materials_id');
    }
}

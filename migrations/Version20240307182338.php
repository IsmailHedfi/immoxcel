<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307182338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_materials (project_id INT NOT NULL, materials_id INT NOT NULL, INDEX IDX_8A2B5362166D1F9C (project_id), INDEX IDX_8A2B53623A9FC940 (materials_id), PRIMARY KEY(project_id, materials_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_materials ADD CONSTRAINT FK_8A2B5362166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_materials ADD CONSTRAINT FK_8A2B53623A9FC940 FOREIGN KEY (materials_id) REFERENCES materials (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_materials DROP FOREIGN KEY FK_8A2B5362166D1F9C');
        $this->addSql('ALTER TABLE project_materials DROP FOREIGN KEY FK_8A2B53623A9FC940');
        $this->addSql('DROP TABLE project_materials');
    }
}

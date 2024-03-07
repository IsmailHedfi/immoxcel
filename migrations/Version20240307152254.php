<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307152254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employees_project (employees_id INT NOT NULL, project_id INT NOT NULL, INDEX IDX_AF0111E38520A30B (employees_id), INDEX IDX_AF0111E3166D1F9C (project_id), PRIMARY KEY(employees_id, project_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE employees_project ADD CONSTRAINT FK_AF0111E38520A30B FOREIGN KEY (employees_id) REFERENCES employees (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE employees_project ADD CONSTRAINT FK_AF0111E3166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE employees_project DROP FOREIGN KEY FK_AF0111E38520A30B');
        $this->addSql('ALTER TABLE employees_project DROP FOREIGN KEY FK_AF0111E3166D1F9C');
        $this->addSql('DROP TABLE employees_project');
    }
}

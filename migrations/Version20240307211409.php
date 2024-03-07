<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240307211409 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capital (id INT AUTO_INCREMENT NOT NULL, salary INT NOT NULL, expensess INT NOT NULL, funds INT NOT NULL, profits INT DEFAULT NULL, big_capital INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expenses (id INT AUTO_INCREMENT NOT NULL, supplier_id INT DEFAULT NULL, capital_id INT DEFAULT NULL, date_e DATE NOT NULL, type VARCHAR(255) NOT NULL, quantity_e DOUBLE PRECISION NOT NULL, coast DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, totalamount DOUBLE PRECISION NOT NULL, archived TINYINT(1) NOT NULL, INDEX IDX_2496F35B2ADD6D8C (supplier_id), INDEX IDX_2496F35BFC2D9FF7 (capital_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, materials_s VARCHAR(255) NOT NULL, phone_number BIGINT NOT NULL, patent_ref VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35BFC2D9FF7 FOREIGN KEY (capital_id) REFERENCES capital (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B2ADD6D8C');
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35BFC2D9FF7');
        $this->addSql('DROP TABLE capital');
        $this->addSql('DROP TABLE expenses');
        $this->addSql('DROP TABLE supplier');
    }
}

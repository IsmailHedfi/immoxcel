<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215220057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bill_invoice (id INT AUTO_INCREMENT NOT NULL, projects_id INT DEFAULT NULL, sell_purchase_id INT DEFAULT NULL, date_b DATE NOT NULL, type_b VARCHAR(255) NOT NULL, client_supplier_name VARCHAR(255) NOT NULL, product_service VARCHAR(255) NOT NULL, coast_b DOUBLE PRECISION NOT NULL, due_date DATE NOT NULL, description VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, payment_method VARCHAR(255) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, INDEX IDX_64A6235B1EDE0F55 (projects_id), INDEX IDX_64A6235B292A005F (sell_purchase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bill_invoice ADD CONSTRAINT FK_64A6235B1EDE0F55 FOREIGN KEY (projects_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE bill_invoice ADD CONSTRAINT FK_64A6235B292A005F FOREIGN KEY (sell_purchase_id) REFERENCES sell_purchase (id)');
        $this->addSql('ALTER TABLE materials ADD projects_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE materials ADD CONSTRAINT FK_9B1716B51EDE0F55 FOREIGN KEY (projects_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_9B1716B51EDE0F55 ON materials (projects_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bill_invoice DROP FOREIGN KEY FK_64A6235B1EDE0F55');
        $this->addSql('ALTER TABLE bill_invoice DROP FOREIGN KEY FK_64A6235B292A005F');
        $this->addSql('DROP TABLE bill_invoice');
        $this->addSql('ALTER TABLE materials DROP FOREIGN KEY FK_9B1716B51EDE0F55');
        $this->addSql('DROP INDEX IDX_9B1716B51EDE0F55 ON materials');
        $this->addSql('ALTER TABLE materials DROP projects_id');
    }
}

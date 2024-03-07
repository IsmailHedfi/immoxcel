<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306171558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capital (id INT AUTO_INCREMENT NOT NULL, salary INT NOT NULL, expensess INT NOT NULL, funds INT NOT NULL, profits INT DEFAULT NULL, big_capital INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE expenses (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, capital_id INT DEFAULT NULL, project_id INT DEFAULT NULL, date_e DATE NOT NULL, type VARCHAR(255) NOT NULL, quantity_e DOUBLE PRECISION NOT NULL, coast DOUBLE PRECISION NOT NULL, description VARCHAR(255) NOT NULL, totalamount DOUBLE PRECISION NOT NULL, archived TINYINT(1) NOT NULL, INDEX IDX_2496F35B4584665A (product_id), INDEX IDX_2496F35B2ADD6D8C (supplier_id), INDEX IDX_2496F35BFC2D9FF7 (capital_id), UNIQUE INDEX UNIQ_2496F35B166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, quantity DOUBLE PRECISION DEFAULT NULL, unit_price DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, project_name VARCHAR(255) NOT NULL, descrption VARCHAR(255) NOT NULL, pred_start DATE NOT NULL, pred_finish DATE NOT NULL, satuts VARCHAR(255) NOT NULL, coast DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sell_purchase (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, type VARCHAR(255) NOT NULL, transaction_date DATE NOT NULL, supplier_name_client_name VARCHAR(255) NOT NULL, porduct_service VARCHAR(255) NOT NULL, quantity INT NOT NULL, coast DOUBLE PRECISION NOT NULL, payment_method VARCHAR(255) NOT NULL, total_amount DOUBLE PRECISION NOT NULL, note VARCHAR(255) NOT NULL, fund DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE supplier (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, materials_s VARCHAR(255) NOT NULL, phone_number BIGINT NOT NULL, patent_ref VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B4584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35BFC2D9FF7 FOREIGN KEY (capital_id) REFERENCES capital (id)');
        $this->addSql('ALTER TABLE expenses ADD CONSTRAINT FK_2496F35B166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B4584665A');
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B2ADD6D8C');
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35BFC2D9FF7');
        $this->addSql('ALTER TABLE expenses DROP FOREIGN KEY FK_2496F35B166D1F9C');
        $this->addSql('DROP TABLE capital');
        $this->addSql('DROP TABLE expenses');
        $this->addSql('DROP TABLE products');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE sell_purchase');
        $this->addSql('DROP TABLE supplier');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

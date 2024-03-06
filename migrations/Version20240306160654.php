<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240306160654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE employees (id INT AUTO_INCREMENT NOT NULL, emp_name VARCHAR(255) NOT NULL, emp_last_name VARCHAR(255) NOT NULL, emp_sex VARCHAR(255) NOT NULL, emp_email VARCHAR(255) DEFAULT NULL, emp_address VARCHAR(255) NOT NULL, emp_phone VARCHAR(255) NOT NULL, emp_function VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, hire_date DATE NOT NULL, end_contract_date DATE NOT NULL, contract_type VARCHAR(255) NOT NULL, allowed_leave_days INT NOT NULL, empcv LONGBLOB DEFAULT NULL, emp_taken_leaves INT NOT NULL, emp_cin VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leaves (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, leave_type VARCHAR(255) NOT NULL, start_date DATE NOT NULL, finish_date DATE NOT NULL, status VARCHAR(255) NOT NULL, leave_description LONGTEXT DEFAULT NULL, INDEX IDX_9D46AD5F8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, reset_token VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D6498C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE leaves ADD CONSTRAINT FK_9D46AD5F8C03F15C FOREIGN KEY (employee_id) REFERENCES employees (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6498C03F15C FOREIGN KEY (employee_id) REFERENCES employees (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE leaves DROP FOREIGN KEY FK_9D46AD5F8C03F15C');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6498C03F15C');
        $this->addSql('DROP TABLE employees');
        $this->addSql('DROP TABLE leaves');
        $this->addSql('DROP TABLE user');
    }
}

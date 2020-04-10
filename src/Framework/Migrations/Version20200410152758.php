<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200410152758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, patient_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_161498D36B899279 (patient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE owner (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(500) NOT NULL, phone VARCHAR(30) NOT NULL, address VARCHAR(500) NOT NULL, registered_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE medical_case (id INT AUTO_INCREMENT NOT NULL, card_id INT NOT NULL, description VARCHAR(2000) NOT NULL, treatment VARCHAR(2000) NOT NULL, started_at DATETIME NOT NULL, ended_at DATETIME NOT NULL, ended TINYINT(1) NOT NULL, INDEX IDX_2BFB332C4ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE patient (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(500) NOT NULL, birth_date DATE NOT NULL, species VARCHAR(500) NOT NULL, INDEX IDX_1ADAD7EB7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D36B899279 FOREIGN KEY (patient_id) REFERENCES patient (id)');
        $this->addSql('ALTER TABLE medical_case ADD CONSTRAINT FK_2BFB332C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)');
        $this->addSql('ALTER TABLE patient ADD CONSTRAINT FK_1ADAD7EB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES owner (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE medical_case DROP FOREIGN KEY FK_2BFB332C4ACC9A20');
        $this->addSql('ALTER TABLE patient DROP FOREIGN KEY FK_1ADAD7EB7E3C61F9');
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D36B899279');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE owner');
        $this->addSql('DROP TABLE medical_case');
        $this->addSql('DROP TABLE patient');
    }
}

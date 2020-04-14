<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200414103304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card CHANGE patient_id patient_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D36B899279 FOREIGN KEY (patient_id) REFERENCES patient (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE medical_case DROP FOREIGN KEY FK_2BFB332C4ACC9A20');
        $this->addSql('ALTER TABLE medical_case ADD CONSTRAINT FK_2BFB332C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D36B899279');
        $this->addSql('ALTER TABLE card CHANGE patient_id patient_id INT NOT NULL');
        $this->addSql('ALTER TABLE medical_case DROP FOREIGN KEY FK_2BFB332C4ACC9A20');
        $this->addSql('ALTER TABLE medical_case ADD CONSTRAINT FK_2BFB332C4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}

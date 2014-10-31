<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20141031140420 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE period (id INT AUTO_INCREMENT NOT NULL, work_start_at DATETIME DEFAULT NULL, period_start DATETIME DEFAULT NULL, period_ending DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE time_unit ADD period_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE time_unit ADD CONSTRAINT FK_7106057EEC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7106057EEC8B7ADE ON time_unit (period_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE time_unit DROP FOREIGN KEY FK_7106057EEC8B7ADE');
        $this->addSql('DROP TABLE period');
        $this->addSql('DROP INDEX IDX_7106057EEC8B7ADE ON time_unit');
        $this->addSql('ALTER TABLE time_unit DROP period_id');
    }
}

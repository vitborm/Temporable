<?php
/**
 *  Copyright 2014 Vitaly Bormotov <bormvit@mail.ru>
 *
 *  This file is part of Quilfe Temporable. 
 *
 *  Quilfe Temporable is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Quilfe Temporable is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Quilfe Temporable. If not, see <http://www.gnu.org/licenses/>.
**/
namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class VersionRemovePeriodEntity extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE time_unit DROP FOREIGN KEY FK_7106057EEC8B7ADE');
        $this->addSql('DROP TABLE period');
        $this->addSql("
            ALTER TABLE user
            ADD
                work_start_at DATETIME
                DEFAULT NULL,
            ADD
                first_period_start_date DATETIME
                NOT NULL
                DEFAULT '2014-11-01 00:00:00'
            ");
        $this->addSql('ALTER TABLE user ALTER first_period_start_date DROP DEFAULT');
        $this->addSql('DROP INDEX IDX_7106057EEC8B7ADE ON time_unit');
        $this->addSql('ALTER TABLE time_unit DROP period_id');
        $this->addSql('CREATE INDEX time_unit_date_idx ON time_unit (date)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE period (id INT AUTO_INCREMENT NOT NULL, work_start_at DATETIME DEFAULT NULL, period_start DATETIME DEFAULT NULL, period_ending DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE time_unit ADD period_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE time_unit ADD CONSTRAINT FK_7106057EEC8B7ADE FOREIGN KEY (period_id) REFERENCES period (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_7106057EEC8B7ADE ON time_unit (period_id)');
        $this->addSql('ALTER TABLE user DROP work_start_at, DROP first_period_start_date');
        $this->addSql('DROP INDEX time_unit_date_idx ON time_unit');
    }
}

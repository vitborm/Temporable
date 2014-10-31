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

class VersionInitiallyGenerate extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("CREATE TABLE project (
            id INT AUTO_INCREMENT NOT NULL,
            name VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("CREATE TABLE user (
            id INT AUTO_INCREMENT NOT NULL,
            username VARCHAR(255) NOT NULL,
            username_canonical VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            email_canonical VARCHAR(255) NOT NULL,
            enabled TINYINT(1) NOT NULL,
            salt VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            last_login DATETIME DEFAULT NULL,
            locked TINYINT(1) NOT NULL,
            expired TINYINT(1) NOT NULL,
            expires_at DATETIME DEFAULT NULL,
            confirmation_token VARCHAR(255) DEFAULT NULL,
            password_requested_at DATETIME DEFAULT NULL,
            roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)',
            credentials_expired TINYINT(1) NOT NULL,
            credentials_expire_at DATETIME DEFAULT NULL,
            phone VARCHAR(50) DEFAULT NULL,
            UNIQUE INDEX UNIQ_8D93D64992FC23A8 (username_canonical),
            UNIQUE INDEX UNIQ_8D93D649A0D96FBF (email_canonical),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE period (
            id INT AUTO_INCREMENT NOT NULL,
            work_start_at DATETIME DEFAULT NULL,
            period_start DATETIME DEFAULT NULL,
            period_ending DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE time_unit (
            id INT AUTO_INCREMENT NOT NULL,
            project_id INT DEFAULT NULL,
            period_id INT DEFAULT NULL,
            user_id INT DEFAULT NULL,
            note VARCHAR(255) DEFAULT NULL,
            time INT NOT NULL,
            date DATETIME NOT NULL,
            INDEX IDX_7106057E166D1F9C (project_id),
            INDEX IDX_7106057EEC8B7ADE (period_id),
            INDEX IDX_7106057EA76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("ALTER TABLE time_unit ADD CONSTRAINT FK_7106057E166D1F9C FOREIGN KEY (project_id)
            REFERENCES project (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE time_unit ADD CONSTRAINT FK_7106057EEC8B7ADE FOREIGN KEY (period_id)
            REFERENCES period (id) ON DELETE SET NULL;");
        $this->addSql("ALTER TABLE time_unit ADD CONSTRAINT FK_7106057EA76ED395 FOREIGN KEY (user_id)
            REFERENCES user (id) ON DELETE SET NULL;");
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql("DROP TABLE time_unit");
        $this->addSql("DROP TABLE period");
        $this->addSql("DROP TABLE user");
        $this->addSql("DROP TABLE project");
    }
}

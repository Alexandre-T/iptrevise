<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170907065445 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE tj_userrole DROP CONSTRAINT fk_userrole_role');
        $this->addSql('DROP SEQUENCE ts_role_rol_id_seq CASCADE');
        $this->addSql('DROP TABLE ts_role');
        $this->addSql('DROP TABLE tj_userrole');
        $this->addSql('ALTER TABLE ts_user ADD roles JSON DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN ts_user.roles IS \'Roles de l\'\'utilisateur\'');
        $this->addSql('ALTER INDEX uk_user_label RENAME TO UNIQ_1E6905663048D892');
        $this->addSql('ALTER INDEX uk_user_mail RENAME TO UNIQ_1E690566D0D90DAF');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE ts_role_rol_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ts_role (rol_id INT NOT NULL, code VARCHAR(16) NOT NULL, rol_label VARCHAR(32) NOT NULL, rol_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rol_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(rol_id))');
        $this->addSql('CREATE UNIQUE INDEX uk_role_label ON ts_role (rol_label)');
        $this->addSql('CREATE UNIQUE INDEX uk_role_code ON ts_role (code)');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_id IS \'Identifiant du rôle\'');
        $this->addSql('COMMENT ON COLUMN ts_role.code IS \'Code of this role\'');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_label IS \'Label du rôle\'');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE tj_userrole (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX idx_userrole_user_id ON tj_userrole (user_id)');
        $this->addSql('CREATE INDEX idx_userrole_role_id ON tj_userrole (role_id)');
        $this->addSql('COMMENT ON COLUMN tj_userrole.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN tj_userrole.role_id IS \'Identifiant du rôle\'');
        $this->addSql('ALTER TABLE tj_userrole ADD CONSTRAINT fk_userrole_user FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_userrole ADD CONSTRAINT fk_userrole_role FOREIGN KEY (role_id) REFERENCES ts_role (rol_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ts_user DROP roles');
        $this->addSql('ALTER INDEX uniq_1e6905663048d892 RENAME TO uk_user_label');
        $this->addSql('ALTER INDEX uniq_1e690566d0d90daf RENAME TO uk_user_mail');
    }
}

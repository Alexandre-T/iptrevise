<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170831090356 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE ts_role_rol_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ts_user_usr_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ext_log_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ts_role (rol_id INT NOT NULL, code VARCHAR(16) NOT NULL, rol_label VARCHAR(32) NOT NULL, rol_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rol_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(rol_id))');
        $this->addSql('CREATE UNIQUE INDEX uk_role_code ON ts_role (code)');
        $this->addSql('CREATE UNIQUE INDEX uk_role_label ON ts_role (rol_label)');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_id IS \'Identifiant du rôle\'');
        $this->addSql('COMMENT ON COLUMN ts_role.code IS \'Code of this role\'');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_label IS \'Label du rôle\'');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN ts_role.rol_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE ts_user (usr_id INT NOT NULL, usr_label VARCHAR(32) NOT NULL, usr_mail VARCHAR(255) NOT NULL, password VARCHAR(64) DEFAULT NULL, usr_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, usr_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(usr_id))');
        $this->addSql('CREATE UNIQUE INDEX uk_user_label ON ts_user (usr_label)');
        $this->addSql('CREATE UNIQUE INDEX uk_user_mail ON ts_user (usr_mail)');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN ts_user.password IS \'Mot de passe crypté\'');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE tj_userrole (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX idx_userrole_user_id ON tj_userrole (user_id)');
        $this->addSql('CREATE INDEX idx_userrole_role_id ON tj_userrole (role_id)');
        $this->addSql('COMMENT ON COLUMN tj_userrole.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN tj_userrole.role_id IS \'Identifiant du rôle\'');
        $this->addSql('CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE tj_userrole ADD CONSTRAINT fk_userrole_user FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_userrole ADD CONSTRAINT fk_userrole_role FOREIGN KEY (role_id) REFERENCES ts_role (rol_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE tj_userrole DROP CONSTRAINT fk_userrole_role');
        $this->addSql('ALTER TABLE tj_userrole DROP CONSTRAINT fk_userrole_user');
        $this->addSql('DROP SEQUENCE ts_role_rol_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ts_user_usr_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ext_log_entries_id_seq CASCADE');
        $this->addSql('DROP TABLE ts_role');
        $this->addSql('DROP TABLE ts_user');
        $this->addSql('DROP TABLE tj_userrole');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}

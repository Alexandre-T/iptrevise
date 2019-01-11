<?php declare(strict_types = 1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181218081911 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE te_service_ser_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE te_site_sit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE tj_role (sit_id INT NOT NULL, usr_id INT NOT NULL, rol_lib BOOLEAN NOT NULL, rol_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, rol_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(sit_id, usr_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E04A0A1686DBC495 ON tj_role (rol_lib)');
        $this->addSql('CREATE INDEX IDX_E04A0A16D50BE019 ON tj_role (sit_id)');
        $this->addSql('CREATE INDEX IDX_E04A0A16C69D3FB ON tj_role (usr_id)');
        $this->addSql('COMMENT ON COLUMN tj_role.sit_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN tj_role.usr_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN tj_role.rol_lib IS \'lecteur = true, writer = false\'');
        $this->addSql('COMMENT ON COLUMN tj_role.rol_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN tj_role.rol_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE te_service (ser_id INT NOT NULL, ser_lib VARCHAR(16) NOT NULL, ser_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ser_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(ser_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FA2B2DB95EE7C5A4 ON te_service (ser_lib)');
        $this->addSql('COMMENT ON COLUMN te_service.ser_id IS \'Identifiant du tag\'');
        $this->addSql('COMMENT ON COLUMN te_service.ser_lib IS \'Libellé du tag\'');
        $this->addSql('COMMENT ON COLUMN te_service.ser_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_service.ser_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE tj_machineservice (service_id INT NOT NULL, machine_id INT NOT NULL, PRIMARY KEY(service_id, machine_id))');
        $this->addSql('CREATE INDEX IDX_C980A3D6ED5CA9E6 ON tj_machineservice (service_id)');
        $this->addSql('CREATE INDEX IDX_C980A3D6F6B75B26 ON tj_machineservice (machine_id)');
        $this->addSql('COMMENT ON COLUMN tj_machineservice.service_id IS \'Identifiant du tag\'');
        $this->addSql('COMMENT ON COLUMN tj_machineservice.machine_id IS \'Identifiant des machines\'');
        $this->addSql('CREATE TABLE te_site (sit_id INT NOT NULL, sit_lib VARCHAR(32) NOT NULL, sit_couleur VARCHAR(6) DEFAULT \'0\' NOT NULL, sit_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, sit_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(sit_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2F363B4DA665F07F ON te_site (sit_lib)');
        $this->addSql('COMMENT ON COLUMN te_site.sit_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN te_site.sit_lib IS \'Libellé du réseau\'');
        $this->addSql('COMMENT ON COLUMN te_site.sit_couleur IS \'Couleur ergonomique du réseau\'');
        $this->addSql('COMMENT ON COLUMN te_site.sit_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_site.sit_updated IS \'Update datetime\'');
        $this->addSql("INSERT INTO te_site (sit_id, sit_lib,sit_couleur,sit_created, sit_updated) VALUES (nextval('te_service_ser_id_seq'), 'Default site','000000',current_timestamp, current_timestamp) ");

        $this->addSql('ALTER TABLE tj_role ADD CONSTRAINT FK_E04A0A16D50BE019 FOREIGN KEY (sit_id) REFERENCES te_site (sit_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_role ADD CONSTRAINT FK_E04A0A16C69D3FB FOREIGN KEY (usr_id) REFERENCES ts_user (usr_id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_machineservice ADD CONSTRAINT FK_C980A3D6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES te_service (ser_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_machineservice ADD CONSTRAINT FK_C980A3D6F6B75B26 FOREIGN KEY (machine_id) REFERENCES te_machine (mac_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_machine ADD mac_location TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE te_machine ADD mac_macs TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_location IS \'Description de la machine\'');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_macs IS \'Adresses mac de la machine(DC2Type:array)\'');
        $this->addSql('ALTER TABLE te_network ADD site_id INT NULL');
        $this->addSql("UPDATE te_network SET site_id =currval('te_service_ser_id_seq')");
        $this->addSql('ALTER TABLE te_network ALTER site_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN te_network.site_id IS \'Identifiant des machines\'');
        $this->addSql('ALTER TABLE te_network ADD CONSTRAINT FK_7B3230D7F6BD1646 FOREIGN KEY (site_id) REFERENCES te_site (sit_id) ON DELETE RESTRICT NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7B3230D7F6BD1646 ON te_network (site_id)');
        $this->addSql('ALTER TABLE ts_user ADD usr_admin BOOLEAN DEFAULT \'false\' NOT NULL');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_admin IS \'is user an admin\'');
        $this->addSql('COMMENT ON COLUMN ts_user.roles IS \'Roles de l\'\'utilisateur(DC2Type:json_array)\'');

        //TODO convert content of json_array in user::roles into new table roles
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE tj_machineservice DROP CONSTRAINT FK_C980A3D6ED5CA9E6');
        $this->addSql('ALTER TABLE te_network DROP CONSTRAINT FK_7B3230D7F6BD1646');
        $this->addSql('ALTER TABLE tj_role DROP CONSTRAINT FK_E04A0A16D50BE019');
        $this->addSql('DROP SEQUENCE te_service_ser_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE te_site_sit_id_seq CASCADE');
        $this->addSql('DROP TABLE tj_role');
        $this->addSql('DROP TABLE te_service');
        $this->addSql('DROP TABLE tj_machineservice');
        $this->addSql('DROP TABLE te_site');
        $this->addSql('ALTER TABLE te_machine DROP mac_location');
        $this->addSql('ALTER TABLE te_machine DROP mac_macs');
        $this->addSql('DROP INDEX IDX_7B3230D7F6BD1646');
        $this->addSql('ALTER TABLE te_network DROP site_id');
        $this->addSql('ALTER TABLE ts_user DROP usr_admin');
        $this->addSql('COMMENT ON COLUMN ts_user.roles IS \'Roles de l\'\'utilisateur\'');
    }
}

<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170921125629 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE te_ip_ip_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE te_machine_mac_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE te_network_net_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ts_user_usr_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE te_tag_tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ext_log_entries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE te_ip (ip_id INT NOT NULL, network_id INT NOT NULL, machine_id INT DEFAULT NULL, user_id INT DEFAULT NULL, ip_ip BIGINT NOT NULL, ip_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ip_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reason VARCHAR(32) DEFAULT NULL, PRIMARY KEY(ip_id))');
        $this->addSql('CREATE INDEX IDX_45527E7C34128B91 ON te_ip (network_id)');
        $this->addSql('CREATE INDEX IDX_45527E7CF6B75B26 ON te_ip (machine_id)');
        $this->addSql('CREATE INDEX IDX_45527E7CA76ED395 ON te_ip (user_id)');
        $this->addSql('COMMENT ON COLUMN te_ip.ip_id IS \'Identifiant des adresses IP\'');
        $this->addSql('COMMENT ON COLUMN te_ip.network_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN te_ip.machine_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN te_ip.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_ip.ip_ip IS \'Adresse IPv4\'');
        $this->addSql('COMMENT ON COLUMN te_ip.ip_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_ip.ip_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE te_machine (mac_id INT NOT NULL, user_id INT DEFAULT NULL, mac_lib VARCHAR(32) NOT NULL, mac_des TEXT DEFAULT NULL, mac_interface SMALLINT DEFAULT 1 NOT NULL, mac_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, mac_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(mac_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EB368EFAF17DE13 ON te_machine (mac_lib)');
        $this->addSql('CREATE INDEX IDX_EB368EFA76ED395 ON te_machine (user_id)');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN te_machine.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_lib IS \'Libellé de la machine\'');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_des IS \'Description de la machine\'');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_interface IS \'Nombre d\'\'interface réseau de la machine\'');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE tj_machinetag (machine_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(machine_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_2CEFBCF8F6B75B26 ON tj_machinetag (machine_id)');
        $this->addSql('CREATE INDEX IDX_2CEFBCF8BAD26311 ON tj_machinetag (tag_id)');
        $this->addSql('COMMENT ON COLUMN tj_machinetag.machine_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN tj_machinetag.tag_id IS \'Identifiant du tag\'');
        $this->addSql('CREATE TABLE te_network (net_id INT NOT NULL, user_id INT DEFAULT NULL, net_lib VARCHAR(32) NOT NULL, net_des TEXT DEFAULT NULL, net_ip BIGINT NOT NULL, net_masque SMALLINT DEFAULT 32 NOT NULL, net_couleur VARCHAR(6) DEFAULT \'0\' NOT NULL, net_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, net_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(net_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B3230D7D7AECD0A ON te_network (net_lib)');
        $this->addSql('CREATE INDEX IDX_7B3230D7A76ED395 ON te_network (user_id)');
        $this->addSql('COMMENT ON COLUMN te_network.net_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN te_network.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_lib IS \'Libellé du réseau\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_des IS \'Description du réseau\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_ip IS \'Adresse Réseau IPv4\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_masque IS \'Masque du réseau\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_couleur IS \'Couleur ergonomique du réseau\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_network.net_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE ts_user (usr_id INT NOT NULL, usr_label VARCHAR(32) NOT NULL, usr_mail VARCHAR(255) NOT NULL, password VARCHAR(64) DEFAULT NULL, usr_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, usr_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, roles JSON DEFAULT NULL, PRIMARY KEY(usr_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E6905663048D892 ON ts_user (usr_label)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1E690566D0D90DAF ON ts_user (usr_mail)');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN ts_user.password IS \'Mot de passe crypté\'');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN ts_user.usr_updated IS \'Update datetime\'');
        $this->addSql('COMMENT ON COLUMN ts_user.roles IS \'Roles de l\'\'utilisateur\'');
        $this->addSql('CREATE TABLE te_tag (tag_id INT NOT NULL, user_id INT DEFAULT NULL, tag_lib VARCHAR(16) NOT NULL, mac_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, mac_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(tag_id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1F05672CA8D1A1CE ON te_tag (tag_lib)');
        $this->addSql('CREATE INDEX IDX_1F05672CA76ED395 ON te_tag (user_id)');
        $this->addSql('COMMENT ON COLUMN te_tag.tag_id IS \'Identifiant du tag\'');
        $this->addSql('COMMENT ON COLUMN te_tag.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_tag.tag_lib IS \'Libellé du tag\'');
        $this->addSql('COMMENT ON COLUMN te_tag.mac_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_tag.mac_updated IS \'Update datetime\'');
        $this->addSql('CREATE TABLE ext_log_entries (id INT NOT NULL, action VARCHAR(8) NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data TEXT DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX log_class_lookup_idx ON ext_log_entries (object_class)');
        $this->addSql('CREATE INDEX log_date_lookup_idx ON ext_log_entries (logged_at)');
        $this->addSql('CREATE INDEX log_user_lookup_idx ON ext_log_entries (username)');
        $this->addSql('CREATE INDEX log_version_lookup_idx ON ext_log_entries (object_id, object_class, version)');
        $this->addSql('COMMENT ON COLUMN ext_log_entries.data IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE te_ip ADD CONSTRAINT FK_45527E7C34128B91 FOREIGN KEY (network_id) REFERENCES te_network (net_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_ip ADD CONSTRAINT FK_45527E7CF6B75B26 FOREIGN KEY (machine_id) REFERENCES te_machine (mac_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_ip ADD CONSTRAINT FK_45527E7CA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_machine ADD CONSTRAINT FK_EB368EFA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_machinetag ADD CONSTRAINT FK_2CEFBCF8F6B75B26 FOREIGN KEY (machine_id) REFERENCES te_machine (mac_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tj_machinetag ADD CONSTRAINT FK_2CEFBCF8BAD26311 FOREIGN KEY (tag_id) REFERENCES te_tag (tag_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_network ADD CONSTRAINT FK_7B3230D7A76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_tag ADD CONSTRAINT FK_1F05672CA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE te_ip DROP CONSTRAINT FK_45527E7CF6B75B26');
        $this->addSql('ALTER TABLE tj_machinetag DROP CONSTRAINT FK_2CEFBCF8F6B75B26');
        $this->addSql('ALTER TABLE te_ip DROP CONSTRAINT FK_45527E7C34128B91');
        $this->addSql('ALTER TABLE te_ip DROP CONSTRAINT FK_45527E7CA76ED395');
        $this->addSql('ALTER TABLE te_machine DROP CONSTRAINT FK_EB368EFA76ED395');
        $this->addSql('ALTER TABLE te_network DROP CONSTRAINT FK_7B3230D7A76ED395');
        $this->addSql('ALTER TABLE te_tag DROP CONSTRAINT FK_1F05672CA76ED395');
        $this->addSql('ALTER TABLE tj_machinetag DROP CONSTRAINT FK_2CEFBCF8BAD26311');
        $this->addSql('DROP SEQUENCE te_ip_ip_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE te_machine_mac_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE te_network_net_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ts_user_usr_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE te_tag_tag_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ext_log_entries_id_seq CASCADE');
        $this->addSql('DROP TABLE te_ip');
        $this->addSql('DROP TABLE te_machine');
        $this->addSql('DROP TABLE tj_machinetag');
        $this->addSql('DROP TABLE te_network');
        $this->addSql('DROP TABLE ts_user');
        $this->addSql('DROP TABLE te_tag');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}

<?php declare(strict_types = 1);

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190111171159 extends AbstractMigration
{
    /**
     * Adding plage and rectification on service.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE te_plage_plage_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE te_plage (plage_id INT NOT NULL, network_id INT NOT NULL, plage_lib VARCHAR(32) NOT NULL, plage_start BIGINT NOT NULL, plage_end BIGINT NOT NULL, ip_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ip_updated TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, reason VARCHAR(32) DEFAULT NULL, PRIMARY KEY(plage_id))');
        $this->addSql('CREATE INDEX ndx_plage_1 ON te_plage (network_id)');
        $this->addSql('COMMENT ON COLUMN te_plage.plage_id IS \'Identifiant des plages\'');
        $this->addSql('COMMENT ON COLUMN te_plage.network_id IS \'Identifiant des machines\'');
        $this->addSql('COMMENT ON COLUMN te_plage.plage_lib IS \'Libellé de la plage réservée\'');
        $this->addSql('COMMENT ON COLUMN te_plage.plage_start IS \'Adresse IPv4 de début de la plage réservée\'');
        $this->addSql('COMMENT ON COLUMN te_plage.plage_end IS \'Adresse IPv4 de fin de la plage réservée\'');
        $this->addSql('COMMENT ON COLUMN te_plage.ip_created IS \'Creation datetime\'');
        $this->addSql('COMMENT ON COLUMN te_plage.ip_updated IS \'Update datetime\'');
        $this->addSql('COMMENT ON COLUMN te_plage.reason IS \'Raison de la plage réservée\'');
        $this->addSql('ALTER TABLE te_plage ADD CONSTRAINT fk_plage_network FOREIGN KEY (network_id) REFERENCES te_network (net_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * Removing plage and modification on services.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE te_plage_plage_id_seq CASCADE');
        $this->addSql('DROP TABLE te_plage');
    }
}

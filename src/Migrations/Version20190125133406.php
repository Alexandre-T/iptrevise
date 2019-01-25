<?php declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190125133406 extends AbstractMigration
{
    /**
     * Add site users and range users.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_machine ALTER mac_macs TYPE TEXT');
        $this->addSql('ALTER TABLE te_machine ALTER mac_macs DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_location IS \'Localisation  de la machine\'');
        $this->addSql('COMMENT ON COLUMN te_network.site_id IS \'Identifiant du site\'');
        $this->addSql('ALTER TABLE te_plage ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_plage.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('ALTER TABLE te_plage ADD CONSTRAINT FK_BF3DF8B7A76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BF3DF8B7A76ED395 ON te_plage (user_id)');
        $this->addSql('ALTER INDEX ndx_plage_1 RENAME TO IDX_BF3DF8B734128B91');
        $this->addSql('COMMENT ON COLUMN tj_role.sit_id IS \'Identifiant du site\'');
        $this->addSql('ALTER TABLE te_site ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_site.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_site.sit_id IS \'Identifiant du site\'');
        $this->addSql('ALTER TABLE te_site ADD CONSTRAINT FK_2F363B4DA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2F363B4DA76ED395 ON te_site (user_id)');
    }

    /**
     * Remove users.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_plage DROP CONSTRAINT FK_BF3DF8B7A76ED395');
        $this->addSql('DROP INDEX IDX_BF3DF8B7A76ED395');
        $this->addSql('ALTER TABLE te_plage DROP user_id');
        $this->addSql('ALTER INDEX idx_bf3df8b734128b91 RENAME TO ndx_plage_1');
        $this->addSql('COMMENT ON COLUMN tj_role.sit_id IS \'Identifiant des machines\'');
        $this->addSql('ALTER TABLE te_site DROP CONSTRAINT FK_2F363B4DA76ED395');
        $this->addSql('DROP INDEX IDX_2F363B4DA76ED395');
        $this->addSql('ALTER TABLE te_site DROP user_id');
        $this->addSql('COMMENT ON COLUMN te_site.sit_id IS \'Identifiant des machines\'');
        $this->addSql('ALTER TABLE te_machine ALTER mac_macs TYPE TEXT');
        $this->addSql('ALTER TABLE te_machine ALTER mac_macs DROP DEFAULT');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_location IS \'Description de la machine\'');
        $this->addSql('COMMENT ON COLUMN te_network.site_id IS \'Identifiant des machines\'');
    }
}

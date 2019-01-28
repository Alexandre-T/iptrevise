<?php declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190125143923 extends AbstractMigration
{
    /**
     * Adding range creator.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_machine ALTER mac_macs TYPE TEXT');
        $this->addSql('ALTER TABLE te_machine ALTER mac_macs DROP DEFAULT');
        $this->addSql('ALTER TABLE te_service ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_service.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('ALTER TABLE te_service ADD CONSTRAINT FK_FA2B2DB9A76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_FA2B2DB9A76ED395 ON te_service (user_id)');
    }

    /**
     * Removing service creator.
     *
     * @param Schema $schema
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_service DROP CONSTRAINT FK_FA2B2DB9A76ED395');
        $this->addSql('DROP INDEX IDX_FA2B2DB9A76ED395');
        $this->addSql('ALTER TABLE te_service DROP user_id');
        $this->addSql('ALTER TABLE te_machine ALTER mac_macs TYPE TEXT');
        $this->addSql('ALTER TABLE te_machine ALTER mac_macs DROP DEFAULT');
    }
}

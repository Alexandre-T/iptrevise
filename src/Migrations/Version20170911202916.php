<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170911202916 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('COMMENT ON COLUMN te_ip.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_machine.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('COMMENT ON COLUMN te_network.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('ALTER TABLE te_tag ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_tag.user_id IS \'Identifiant de l\'\'utilisateur\'');
        $this->addSql('ALTER TABLE te_tag ADD CONSTRAINT FK_1F05672CA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_1F05672CA76ED395 ON te_tag (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('COMMENT ON COLUMN te_machine.user_id IS \'Identifiant du créateur\'');
        $this->addSql('COMMENT ON COLUMN te_network.user_id IS \'Identifiant du créateur\'');
        $this->addSql('ALTER TABLE te_tag DROP CONSTRAINT FK_1F05672CA76ED395');
        $this->addSql('DROP INDEX IDX_1F05672CA76ED395');
        $this->addSql('ALTER TABLE te_tag DROP user_id');
        $this->addSql('COMMENT ON COLUMN te_ip.user_id IS \'Identifiant du créateur\'');
    }
}

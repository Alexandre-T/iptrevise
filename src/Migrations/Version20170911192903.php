<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170911192903 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_machine ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_machine.user_id IS \'Identifiant du créateur\'');
        $this->addSql('ALTER TABLE te_machine ADD CONSTRAINT FK_EB368EFA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EB368EFA76ED395 ON te_machine (user_id)');
        $this->addSql('ALTER TABLE te_network ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_network.user_id IS \'Identifiant du créateur\'');
        $this->addSql('ALTER TABLE te_network ADD CONSTRAINT FK_7B3230D7A76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7B3230D7A76ED395 ON te_network (user_id)');
        $this->addSql('ALTER TABLE te_ip ADD user_id INT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_ip.user_id IS \'Identifiant du créateur\'');
        $this->addSql('ALTER TABLE te_ip ADD CONSTRAINT FK_45527E7CA76ED395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_45527E7CA76ED395 ON te_ip (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_network DROP CONSTRAINT FK_7B3230D7A76ED395');
        $this->addSql('DROP INDEX IDX_7B3230D7A76ED395');
        $this->addSql('ALTER TABLE te_network DROP user_id');
        $this->addSql('ALTER TABLE te_machine DROP CONSTRAINT FK_EB368EFA76ED395');
        $this->addSql('DROP INDEX IDX_EB368EFA76ED395');
        $this->addSql('ALTER TABLE te_machine DROP user_id');
        $this->addSql('ALTER TABLE te_ip DROP CONSTRAINT FK_45527E7CA76ED395');
        $this->addSql('DROP INDEX IDX_45527E7CA76ED395');
        $this->addSql('ALTER TABLE te_ip DROP user_id');
    }
}

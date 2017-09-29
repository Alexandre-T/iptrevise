<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170929082446 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_ip DROP CONSTRAINT FK_45527E7CA76ED395');
        $this->addSql('ALTER TABLE te_ip ADD CONSTRAINT FK_IP_USER FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_machine DROP CONSTRAINT FK_EB368EFA76ED395');
        $this->addSql('ALTER TABLE te_machine ADD CONSTRAINT FK_MACHINE_USER FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_network DROP CONSTRAINT FK_7B3230D7A76ED395');
        $this->addSql('ALTER TABLE te_network ADD CONSTRAINT FK_NETWORK_USER FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_tag DROP CONSTRAINT FK_1F05672CA76ED395');
        $this->addSql('ALTER TABLE te_tag ADD CONSTRAINT FK_TAG_USER FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE te_machine DROP CONSTRAINT FK_MACHINE_USER');
        $this->addSql('ALTER TABLE te_machine ADD CONSTRAINT fk_eb368efa76ed395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_ip DROP CONSTRAINT FK_IP_USER');
        $this->addSql('ALTER TABLE te_ip ADD CONSTRAINT fk_45527e7ca76ed395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_network DROP CONSTRAINT FK_NETWORK_USER');
        $this->addSql('ALTER TABLE te_network ADD CONSTRAINT fk_7b3230d7a76ed395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE te_tag DROP CONSTRAINT FK_TAG_USER');
        $this->addSql('ALTER TABLE te_tag ADD CONSTRAINT fk_1f05672ca76ed395 FOREIGN KEY (user_id) REFERENCES ts_user (usr_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}

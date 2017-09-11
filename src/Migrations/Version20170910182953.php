<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170910182953 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE te_ip DROP ip_lib');
        $this->addSql('ALTER TABLE te_ip DROP ip_des');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE te_ip ADD ip_lib VARCHAR(32) NOT NULL');
        $this->addSql('ALTER TABLE te_ip ADD ip_des TEXT DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN te_ip.ip_lib IS \'Libellé de l\'\'adresse IP\'');
        $this->addSql('COMMENT ON COLUMN te_ip.ip_des IS \'Description de l\'\'adresse IP\'');
    }
}

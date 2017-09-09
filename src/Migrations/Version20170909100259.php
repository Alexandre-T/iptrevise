<?php

namespace App\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170909100259 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_7b3230d7dec3e8ce');
        $this->addSql('ALTER TABLE te_network ALTER net_masque SET DEFAULT 32');
        $this->addSql('ALTER TABLE te_network ALTER net_couleur SET DEFAULT \'000000\'');
        $this->addSql('ALTER TABLE ts_user ALTER roles TYPE JSON');
        $this->addSql('ALTER TABLE ts_user ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE te_ip ALTER ip_lib TYPE VARCHAR(32)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ts_user ALTER roles TYPE JSON');
        $this->addSql('ALTER TABLE ts_user ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE te_network ALTER net_masque DROP DEFAULT');
        $this->addSql('ALTER TABLE te_network ALTER net_couleur DROP DEFAULT');
        $this->addSql('CREATE UNIQUE INDEX uniq_7b3230d7dec3e8ce ON te_network (net_couleur)');
        $this->addSql('ALTER TABLE te_ip ALTER ip_lib TYPE VARCHAR(255)');
    }
}

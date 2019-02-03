<?php

/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190209120338 extends AbstractMigration
{
    /**
     * Updating primary key.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('COMMENT ON COLUMN te_machine.mac_macs IS \'Adresses mac de la machine\'');
        $this->addSql('alter table tj_machineservice drop constraint tj_machineservice_pkey');
        $this->addSql('alter table tj_machineservice add constraint tj_machineservice_pkey primary key (machine_id, service_id)');
    }

    /**
     * Reverting primary key.
     *
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('alter table tj_machineservice drop constraint tj_machineservice_pkey');
        $this->addSql('alter table tj_machineservice add constraint tj_machineservice_pkey primary key (service_id,machine_id)');
        $this->addSql('COMMENT ON COLUMN te_machine.mac_macs IS \'Adresses mac de la machine(DC2Type:array)\'');
    }
}

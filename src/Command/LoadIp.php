<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.2
 *
 * @category Command
 *
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 */

namespace App\Command;

use App\Entity\InformationInterface;
use App\Entity\Ip;
use App\Entity\Machine;
use App\Entity\Network;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoadIp extends AbstractLoader
{
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:load:ip')
            // the short description shown while running "php bin/console list"
            ->setDescription('Charge en base le contenu des fichiers téléchargées .')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.')
        ;
    }

    /**
     * Validate entity.
     *
     * @param array $ligne csv line
     * @return ConstraintViolationList
     */
    function validateEntity(array $ligne): ConstraintViolationList
    {
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();
        $violations->addAll($validator->validate($ligne[0], [new NotBlank()]));
        //FIXME TESTER QUE LE LIBELLE DU RESEAU EST NON VIDE !
        //FIXME S'IL EST NON VIDE TESTER QUE LE RESEAU EXISTE !
        //FIXME TESTER QUE LE LIBELLE DE LA MACHINE EST NON VIDE !
        //FIXME S'IL EST NON VIDE TESTER QUE LA MACHINE EXISTE !
        $violations->addAll($validator->validate($ligne[3], [new Length('max="32"')]));
        
        return $violations;
    }

    /**
     * Create an Ip from data and return it.
     * 
     * @param array $ligne
     * @return InformationInterface
     */
    function loadEntity(array $ligne): InformationInterface
    {
        $ip = new ip();
        $ip
            ->setIp(ip2long($ligne[0]))
            ->setReason($ligne[3]);

        /** @var Network $network */
        $network = $this->entityManager->getRepository('App:Network')->findOneBy(['label' => $ligne[1]]);
        $ip->setNetwork($network);

        /** @var Machine $machine */
        $machine = $this->entityManager->getRepository('App:Machine')->findOneBy(['label' => $ligne[1]]);
        $ip->setMachine($machine);

        return $ip;
        
    }

    /**
     * File where ip are stored.
     * 
     * @return string
     */
    function getFilename(): string
    {
        return __DIR__ . '/../../Data/ip.csv';
    }
}

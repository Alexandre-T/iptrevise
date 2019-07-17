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
use Symfony\Component\Validator\Constraints\NotNull;

class LoadIp extends AbstractLoader
{
    /**
     * Validate entity.
     *
     * @param array $ligne csv line
     * @return ConstraintViolationList
     */
    function validateEntity(array $ligne): ConstraintViolationList
    {
        $machineRepository = $this->entityManager->getRepository('App:Machine');
        $networkRepository = $this->entityManager->getRepository('App:Network');
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();
        $violations->addAll($validator->validate($ligne[1], [
            new NotBlank()
        ]));
        $violations->addAll($validator->validate($networkRepository->findOneBy(['label' => $ligne[1]]), [
            new NotNull([
                //Translation should be launched here.
                'message' => $this->translator->trans(
                    'form.ip.error.network.exist %network%',
                    ['%network%' => $ligne[1]],
                    'validators'
                )
            ])
        ]));

        $emptyViolation = $validator->validate($ligne[2], [new NotBlank()]);

        if (empty($emptyViolation)) {
            $violations->addAll($emptyViolation);
        } else {
            $violations->addAll($validator->validate($machineRepository->findOneBy(['label' => $ligne[2]]), [
                new NotNull([
                    'message' => $this->translator->trans(
                        'form.ip.error.machine.exist %machine%',
                        ['%machine%' => $ligne[2]],
                        'validators'
                    )
                ])
            ]));
        }
        $violations->addAll($validator->validate($ligne[0], [new NotBlank()]));
        $violations->addAll($validator->validate($ligne[3], [new Length(['max' => 32])]));

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
        $machine = $this->entityManager->getRepository('App:Machine')->findOneBy(['label' => $ligne[2]]);
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
        return __DIR__ . '/../../data/ip.csv';
    }

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
            ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.');
    }
}

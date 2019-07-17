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
use App\Entity\Machine;
use App\Entity\Service;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotNull;

class LoadMachine extends AbstractLoader
{
    /**
     * ConstraintViolationList.
     *
     * @param array $ligne
     * @return ConstraintViolationList
     */
    function validateEntity(array $ligne): ConstraintViolationList
    {
        $serviceRepository = $this->entityManager->getRepository('App:Service');
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();

        $violations->addAll($validator->validate(count($ligne), [
            new EqualTo([
                'value' => 6,
                'message' => 'Cette ligne devrait contenir 6 éléments et non pas ' . count($ligne)
            ])
        ]));

        if ($violations->count()) {
            return $violations;
        }

        $violations->addAll($validator->validate($ligne[0], [
            new Length([
                'max' => 32,
                'maxMessage' => $this->translator->trans('form.machine.error.label.max-length',[],'validators')
            ]),
            new NotBlank()
        ]));
        $violations->addAll($validator->validate(intval($ligne[1]), [
            new GreaterThanOrEqual(['value' => '0',
                'message' => 'form.machine.error.interface.min',]),
            new NotBlank(),
        ]));
        $services = preg_split('/;/', $ligne[5]);
        foreach ($services as $service) {
            if ($service !== '') {
                $violations->addAll($validator->validate($serviceRepository->findOneBy(['label' => $service]), [
                    new NotNull([
                        //'message' => 'form.machine.error.service.exist %service%', ['%service%' => $service]
                        'message' => sprintf('form.machine.error.service.exist %s', $service)
                    ])
                ]));
            }
        }
        //Done
        return $violations;
    }

    /**
     * Transform line into entity and save it.
     *
     * @param array $ligne
     *
     * @return InformationInterface
     */
    function loadEntity(array $ligne): InformationInterface
    {
        $serviceRepository = $this->entityManager->getRepository('App:Service');
        $machine = new machine();
        $machine
            ->setLabel($ligne[0])
            ->setInterface(intval($ligne[1]))
            //TODO Gérer le cas de plusieurs adresses macs (remplacer le séparateur ; par \n)
            ->setMacs($ligne[2])
            ->setLocation($ligne[3])
            ->setDescription($ligne[4]);

        //Bonne idée l'importation de service !
        $services = preg_split('/;/', $ligne[5]); //Original le preg_split !
        foreach ($services as $service) {
            if (!empty($service)) {
                /** @var Service $serv */
                $serv = $serviceRepository->findOneBy(['label' => $service]);
                $machine->addService($serv);
            }
        }

        return $machine;
    }

    /**
     * Return the name of the file (site, ip, machine, etc.).
     *
     * @return string
     */
    function getFilename(): string
    {
        return __DIR__ . '/../../data/machine.csv';
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:load:machine')
            // the short description shown while running "php bin/console list"
            ->setDescription('Charge en base le contenu des fichiers téléchargées .')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.');
    }
}

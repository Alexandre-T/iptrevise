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
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class LoadMachine extends AbstractLoader
{

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
            ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.')
        ;
    }

//    /**
//     * Execute the command.
//     *
//     * @param InputInterface  $input
//     * @param OutputInterface $output
//     *
//     * @return int|null
//     *
//     * @throws \Exception
//     */
//    protected function execute(InputInterface $input, OutputInterface $output): ?int
//    {
//        $sansErreur = true;
//        $output->writeln([
//            '<info>Loader lancé</info>',
//            '<info>================</info>',
//            '<info>Étape 1 : interrogation du fichier de machines...</info>',
//            '<info>---------</info>',
//        ]);
//
//        $fd = fopen('Data/machine.csv', 'r');
//        $validator = Validation::createValidator();
//        $nligne = 1;
//
//        if (!$fd) {
//            $output->writeln('Pas de fichier Data/machines.csv présent');
//            return 1;
//        }
//
//       $this->entityManager->beginTransaction();
//        while (!feof($fd)) {
//            $Ligne = fgets($fd, 255);
//            $ligne = preg_split('/,/', $Ligne);
//            //[0]:label [1]:inteface [2]:macs [3]:location [4]:description
//            //[5]:services
//            //WIP:
//            //Peut-etre une liste de tags en septieme element
//            //A demander au client
//            if ('' !== $ligne[0]) {
//                $violations0 = $validator->validate($ligne[0], [new length(['max' => 32]),
//      new NotBlank(), ]);
//                $violations1 = $validator->validate(intval($ligne[1]),
//      [new GreaterThanOrEqual(['value' => '0',
//      'message' => 'form.machine.error.interface.min', ]),
//      new NotBlank(), ]);
//                $sansErreur = $this->errorMessage($violations0, $nligne, $output)
//      && $this->errorMessage($violations1, $nligne, $output)
//      && $sansErreur;
//                $serviceManager = new ServiceManager($this->entityManager);
//                $all = $serviceManager->getAll();
//                if ($sansErreur) {
//                    $machine = new machine();
//                    $machine
//        ->setLabel($ligne[0])
//        ->setInterface(intval($ligne[1]))
//        ->setMacs($ligne[2])
//        ->setLocation($ligne[3])
//        ->setDescription($ligne[4])
//        ;
//                    $services = preg_split('/;/', $ligne[5]);
//                    foreach ($services as $service) {
//                        $found = false;
//                        foreach ($all as $al) {
//                            if ($al->getLabel() === $service) {
//                                $found = true;
//                                $machine->addService($al);
//                            }
//                        }
//                        if (!$found) {
//                            //WIP :
//                            //je cree le nouveau service si il n'existait pas avant dans la
//                            //base de donnee. Je dois peut-etre afficher un message ici ?
//                            //A demander au client
//                            $Service = new service();
//                            $Service->setLabel($service);
//                            $this->entityManager->persist($Service);
//                            $machine->addService($Service);
//                        }
//                    }
//                    $this->entityManager->persist($machine);
//                }
//                ++$nligne;
//            }
//        }
//        fclose($fd);
//
//        return $this->finalization($sansErreur, $output, $input);
//    }
    /**
     * ConstraintViolationList.
     *
     * @param array $ligne
     * @return ConstraintViolationList
     */
    function validateEntity(array $ligne): ConstraintViolationList
    {
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();

        $violations->addAll($validator->validate($ligne[0], [
            new Length(['max' => 32]),
            new NotBlank()
        ]));
        $violations->addAll($validator->validate(intval($ligne[1]), [
            new GreaterThanOrEqual(['value' => '0',
                'message' => 'form.machine.error.interface.min',]),
            new NotBlank(),
        ]));

        //FIXME Tester si chacun des services dans $ligne5 existe déjà dans la base sinon erreur.

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
            //Attention, ce code va foirer si on a deux fois le même nouveau service, car il ne sera jamais dans $all
            //Je préfère qu'on ait le même fonctionnement partout
            //FIXME Créer une Commande LoadService
            //Faire une recherche dans la base (regarder LoadNetwork ligne de code
            /*foreach ($all as $al) {
                if ($al->getLabel() === $service) {
                    $found = true;
                    $machine->addService($al);
                }
            }
            if (!$found) {
                //WIP :
                //je cree le nouveau service si il n'existait pas avant dans la
                //base de donnee. Je dois peut-etre afficher un message ici ?
                //A demander au client
                $Service = new service();
                $Service->setLabel($service);
                $this->entityManager->persist($Service);
                $machine->addService($Service);
            }*/
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
        return __DIR__ . '/../../Data/machine.csv';
    }
}

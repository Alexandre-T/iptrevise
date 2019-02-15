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
use App\Entity\Network;
use App\Entity\Site;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;

class LoadNetwork extends AbstractLoader
{
    function validateEntity(array $ligne): ConstraintViolationList
    {
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();
        $violations->addAll($validator->validate($ligne[0], [
            new Length(['max' => 32]),
            new NotBlank(),]));
        $violations->addAll($validator->validate($ligne[1], [new NotBlank()]));
        $violations->addAll($validator->validate(intval($ligne[2]), [
            new Range([
                'min' => 0,
                'max' => 32,
                'minMessage' => 'form.network.error.cidr.min',
                'maxMessage' => 'form.network.error.cidr.max',
            ]), new NotBlank()]));
        $violations->addAll($validator->validate($ligne[3], [
            new Regex([
                'pattern' => '/^([0-9a-f]{3}|[0-9a-f]{6})$/i',
                'message' => 'form.network.error.color.pattern', //message dans validator.fr.yml
            ]),
            new NotBlank()
        ]));
        //FIXME TESTER QUE LE LIBELLE DU SITE EST NON VIDE !
        //FIXME S'IL EST NON VIDE TESTER QUE LE SITE EXISTE !

        return $violations;
    }

    /**
     * Create network and return it.
     * @param array $ligne
     * @return InformationInterface
     */
    function loadEntity(array $ligne): InformationInterface
    {
        $siteRepository = $this->entityManager->getRepository('App:Site');
        /** @var Site $site */
        $site = $siteRepository->findOneBy(['label' => $ligne[4]]);
        $network = new network();
        $network
            ->setLabel($ligne[0])
            ->setIp(ip2long($ligne[1]))
            ->setCidr(intval($ligne[2]))
            ->setColor($ligne[3])
            ->setDescription($ligne[5])
            ->setSite($site);
    }

    function getFilename(): string
    {
        return __DIR__ . '/../../Data/network.csv';
    }

    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:load:network')
            // the short description shown while running "php bin/console list"
            ->setDescription('Charge en base le contenu des fichiers téléchargées .')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.');
    }


//
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
//            '<info>Étape 1 : interrogation du fichier de networks...</info>',
//            '<info>---------</info>',
//        ]);
//
//        $fd = fopen('Data/network.csv', 'r');
//        $validator = Validation::createValidator();
//        $nligne = 1;
//        if (!$fd) {
//            $output->writeln('Pas de fichier Data/networks.csv présent');
//            return 1;
//        }
//        $this->entityManager->beginTransaction();
//        //Optimisation : on désactive le log des requêtes
//        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
//        //setLabel setDescription setIp setCidr setColor setSite
//        while (!feof($fd)) {
//            $Ligne = fgets($fd, 255);
//            $ligne = preg_split('/,/', $Ligne); // [0]: label [1]: ip
//            // [2]: cidr [3]: color [4]: site [5]: description
//            if ('' !== $ligne[0]) {
//                $violations0 = $validator->validate($ligne[0], [new length(['max' => 32]),
//      new NotBlank(), ]);
//                $violations1 = $validator->validate($ligne[1], [new NotBlank()]);
//                $violations2 = $validator->validate(intval($ligne[2]), [new Range([
//        'min' => 0,
//        'max' => 32,
//        'minMessage' => 'form.network.error.cidr.min',
//        'maxMessage' => 'form.network.error.cidr.max',
//      ]), new NotBlank()]);
//                $violations3 = $validator->validate($ligne[3], [new regex([
//        'pattern' => '/^([0-9a-f]{3}|[0-9a-f]{6})$/i',
//        'message' => 'form.network.error.color.pattern', //message dans validator.fr.yml
//      ]), new NotBlank()]);
//                $sansErreur = ($this->errorMessage($violations0, $nligne, $output)
//      && $this->errorMessage($violations1, $nligne, $output)
//      && $this->errorMessage($violations2, $nligne, $output)
//      && $this->errorMessage($violations3, $nligne, $output))
//      && $sansErreur;
//                $siteManager = new SiteManager($this->entityManager);
//                $all = $siteManager->getAll();
//
//                if ($sansErreur) {
//                    $network = new network();
//                    $network
//                        ->setLabel($ligne[0])
//                        ->setIp(ip2long($ligne[1]))
//                        ->setCidr(intval($ligne[2]))
//                        ->setColor($ligne[3])
//                        ->setDescription($ligne[5])
//                    ;
//                    foreach ($all as $site) {
//                        if ($site->getLabel() === $ligne[4]) {
//                            $network->setSite($site);
//                        }
//                    }
//                    $this->entityManager->persist($network);
//                }
//                ++$nligne;
//            }
//        }
//        //Closing file
//        fclose($fd);
//
//        return $this->finalization($sansErreur, $output, $input);
//    }
}

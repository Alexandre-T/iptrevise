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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

abstract class AbstractLoader extends Command
{
    /**
     * Entity manager.
     *
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * Translator.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * ConstraintViolationList.
     *
     * @param array $ligne
     * @return ConstraintViolationList
     */
    abstract function validateEntity(array $ligne): ConstraintViolationList;

    /**
     * Transform line into abstract entity and save it.
     *
     * @param array $ligne
     * @return InformationInterface
     */
    abstract function loadEntity(array $ligne): InformationInterface;

    /**
     * Return the name of the file (site, ip, machine, etc.).
     *
     * @return string
     */
    abstract function getFilename(): string;

    /**
     * DownloadCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator
     */
    public function __construct(EntityManagerInterface $entityManager, TranslatorInterface $translator)
    {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->translator = $translator;
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        $sansErreur = true;
        $filename = basename($this->getFilename());
        $output->writeln([
            '<info>Chargement lancé</info>',
            "<info>Lecture du fichier $filename</info>",
        ]);

        //FIXME Corriger ce code qui n'indique pas à l'administrateur pourquoi son fichier ne se charge pas.
        $fd = fopen($this->getFilename(), 'r');

        $nligne = 1;
        if (!$fd) {
            $output->writeln('Pas de fichier Data/sites.csv présent');

            return 2;
        }

        $this->entityManager->beginTransaction();
        while (!feof($fd)) {
            $ligne = fgetcsv($fd, null, ',');
            if (!empty($ligne[0])) {
                $violations = $this->validateEntity($ligne);
                if (empty($violations)) {
                    $entity = $this->loadEntity($ligne);
                    //TODO Ajouter un créateur, on pourrait créer un utilisateur bidon qui n'a aucun droit de connexion
                    //Cela permettrait de voir dans les journaux de bord qu'ils ont été créés par l'importateur de données.
                    //$creator = ????;
                    //$entity->setCreator($creator);

                    $this->entityManager->persist($entity);
                }
                foreach ($violations as $violation) {
                    $output->writeln(sprintf('<error>Erreur ligne %d : %s</error>',
                            $nligne,
                            $violation->getMessage())
                    );
                    $sansErreur = false;
                }
                ++$nligne;
            }
        }
        //Closing file
        fclose($fd);

        $this->entityManager->flush();

        $output->writeln("\n");

        if ($sansErreur) {
            $this->entityManager->commit();
            $output->writeln('<info>Transaction validé.</info>');
        } else {
            $this->entityManager->rollback();
            $output->writeln('<warning>Transaction non validé.</warning>');
        }
        $output->writeln(sprintf('<comment>Mémoire consommée : %s</comment>', memory_get_usage()));

        $output->writeln('Fin du processus.');

        return $sansErreur ? 0 : 1;
    }
}

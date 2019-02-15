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
     * @param InputInterface $input
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
            '<info>' . $this->translator->trans('command.loader-launched') . '</info>',
            '<info>' . $this->translator->trans('command.reading-file %filename%', ['%filename%' => $filename]) . '</info>',
        ]);

        //FIXME Corriger ce code qui n'indique pas à l'administrateur pourquoi son fichier ne se charge pas.
        //On doit savoir si le fichier existe ou non
        //On doit savoir si le fichier est accessible en lecture ou non.
        $fd = fopen($this->getFilename(), 'r');

        $nligne = 1;
        if (!$fd) {
            $output->writeln(
                '<error>'
                . $this->translator->trans('command.missing-file %filename%', ['%filename%' => $filename])
                . '</error>');

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
                    $output->writeln(
                        '<error>'
                        . $this->translator->trans('command.error.line %line% %error%', [
                            '%line%' => $nligne,
                            '%error%' => $this->translator->trans($violation->getMessage(), [], 'validators')
                        ])
                        . '</error>'
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
            //FIXME AJOUTER LE NOMBRE D'ENTITES CREEES
            //FIXME TRADUIRE
            $output->writeln('<info>Transaction validé.</info>');
        } else {
            $this->entityManager->rollback();
            //FIXME TRADUIRE
            $output->writeln('<error>Transaction non validé.</error>');
        }
        //FIXME TRADUIRE
        $output->writeln('Fin du processus.');

        return $sansErreur ? 0 : 1;
    }

    /**
     * Return the name of the file (site, ip, machine, etc.).
     *
     * @return string
     */
    abstract function getFilename(): string;

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
}

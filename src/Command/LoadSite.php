<?php
/**
 *
 * PHP version 7.2
 *
 *
 * @category Entity
 *
 * @license   MIT
 *
 */
 
namespace App\Command;
use App\Entity\File;
use App\Entity\Passage;
use App\Exception\LoadException;
use App\Repository\CameraRepository;
use App\Repository\FileRepository;
use App\Utils\Header;
use App\Utils\LoadUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
class LoadCommand extends Command
{
    use LockableTrait;
    /**
     * Nombre de colonnes.
     */
    const COLUMNS = [44, 45, 49, 50]; //On accepte 44 et 49, et on a ajouté une colonne
    const BATCH_SIZE = 42000;
    /**
     * The entity manager.
     *
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * The camera repository.
     *
     * @var CameraRepository
     */
    private $cameraRepository;
    /**
     * The file repository.
     *
     * @var FileRepository
     */
    private $fileRepository;
    /**
     * Loader service.
     *
     * @var LoadUtils
     */
    private $loader;
    /**
     * DownloadCommand constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param LoadUtils              $loader
     */
    public function __construct(EntityManagerInterface $entityManager, LoadUtils $loader)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->cameraRepository = $entityManager->getRepository('App:Camera');
        $this->fileRepository = $entityManager->getRepository('App:File');
        $this->loader = $loader;
    }
    /**
     * Configure the command.
     */
    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:load:site')
            // the short description shown while running "php bin/console list"
            ->setDescription('Charge en base le contenu des fichiers téléchargées .')
            //Add an option for purge to purge database
            ->addOption('purge', 'p', InputArgument::OPTIONAL, 'Répondre y si vous voulez purger les données en base (y/n)', 'n')
            //Add an option for reload file already loaded
            ->addOption('overload', 'o', InputArgument::OPTIONAL, 'Répondre y si vous voulez recharger les fichiers déjà chargés (y/n)', 'n')
            //Add an option for reload file already loaded
            ->addOption('transaction', 't', InputArgument::OPTIONAL, 'Valider la transaction après chaque fichier, à la fin du processus ou sans transaction ? (f/p/n)', 'f')
            //Add an option for reload file already loaded
            ->addOption('memory_limit', 'm', InputArgument::OPTIONAL, 'Laisser PHP utiliser la quantité de mémoire souhaitée ? (y/n)', 'y')
            //Add an option for reload file already loaded
            ->addOption('fetch', 'f', InputArgument::OPTIONAL, 'Nombre de lignes entre chaque transmission à la base de données', self::BATCH_SIZE)
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.')
        ;
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
        if (!$this->lock()) {
            $output->writeln('<error>The command is already running in another process.</error>');
            return 0;
        }
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '<info>Loader lancé</info>',
            '<info>================</info>',
            '<info>Étape 1 : interrogation de la base de données pour déterminer le nombre de caméras...</info>',
            '<info>---------</info>',
        ]);
        $cameras = $this->cameraRepository->searchActive();
        $nCamera = count($cameras);
        if ($nCamera) {
            $output->writeln([
                "<comment>$nCamera caméras actives</comment>",
                '<info>Étape 2: Interrogations des fichiers à charger en base.</info>',
                '<info>--------</info>',
            ]);
        } else {
            $output->writeln('<info>Aucune caméra à interroger. Fin du processus.</info>');
            return 0;
        }
        //Set memory limit.
        if ('y' === $input->getOption('memory_limit')) {
            @ini_set('memory_limit', -1);
        }
        //On débute une transaction
        if ('f' !== $input->getOption('transaction') && 'n' !== $input->getOption('transaction')) {
            $this->entityManager->beginTransaction();
        }
        //Optimisation : on désactive le log des requêtes
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
        foreach ($cameras as $camera) {
            $output->writeln(sprintf('<comment>Parcours des fichiers la caméra « %s »</comment>', $camera->getName()));
            $directoryname = __DIR__.'/../../data/downloaded/camera-'.$camera->getCode();
            $directoryInfo = new \SplFileInfo($directoryname);
            if (!$directoryInfo->isDir()) {
                $output->writeln(sprintf('<error>%s n’est pas un répertoire</error>', $directoryname));
                continue;
            }
            if (!$directoryInfo->isReadable()) {
                $output->writeln(sprintf('<error>Le répertoire %s n’est pas lisible</error>', $directoryname));
                continue;
            }
            //Parcours du répertoire
            foreach (scandir($directoryname) as $filename) {
                $fileInfo = new \SplFileInfo($directoryname.'/'.$filename);
                //on élimine silencieusement les répertoires
                if ($fileInfo->isDir()) {
                    $output->writeln(sprintf('<comment>Répertoire %s ignoré</comment>', $fileInfo->getFilename()));
                    continue;
                }
                //on ne garde que les fichiers csv
                if ('csv' !== $fileInfo->getExtension()) {
                    $output->writeln(sprintf('<comment>Fichier %s ignoré en raison de son extension inconnue</comment>', $fileInfo->getFilename()));
                    continue;
                }
                //Le fichier est vide
                if (0 === $fileInfo->getSize()) {
                    $output->writeln(sprintf('<info>Fichier %s vide et donc ignoré</info>', $fileInfo->getFilename()));
                    continue;
                }
                //Le fichier est-il lisible ?
                if (!$fileInfo->isReadable()) {
                    $output->writeln(sprintf('<error>Fichier %s ignoré, le fichier n’est pas autorisé à la lecture</error>.', $fileInfo->getFilename()));
                    continue;
                }
                $fileObject = $fileInfo->openFile('r');
                $output->write(sprintf('<comment>Fichier %s en cours d’analyse :</comment> ', $fileInfo->getFilename()));
                $index = 0;
                $empreinte = md5_file($fileInfo->getRealPath());
                $output->write(" $empreinte\n");
                if ($this->fileRepository->existsWithEmpreinte($empreinte)) {
                    if ('y' == $input->getOption('overload')) {
                        // FIXME purger tous les passages de ce fichier ???
                        die('cas non traité. Ne choisissez pas l’option overload pour le moment.');
                    } else {
                        $output->writeln(sprintf('<info>Fichier %s ignoré, ce fichier est déjà chargé en base de données.</info>', $fileInfo->getFilename()));
                        continue;
                    }
                }
                if ('f' === $input->getOption('transaction')) {
                    $this->entityManager->beginTransaction();
                }
                $fileEntity = new File();
                $fileEntity->setFilename($filename);
                $fileEntity->setDirectory('data/downloaded/camera-'.$camera->getCode());
                $fileEntity->setMd5sum($empreinte);
                $this->entityManager->persist($fileEntity);
                $lignes = $this->loader->getLines($fileObject);
                $output->writeln(sprintf('<comment>Fichier %s en cours de chargement (%d lignes).</comment> ', $fileInfo->getFilename(), $lignes));
                // creates a new progress bar (50 units)
                //https://symfony.com/doc/current/components/console/helpers/progressbar.html
                $progressBar = new ProgressBar($output, $lignes);
                // starts and displays the progress bar
                $progressBar->start();
                $progressBar->setFormat('debug');
                $batchSize = 0;
                while ($lignes > 0 && !$fileObject->eof()) {
                    ++$batchSize;
                    $progressBar->advance();
                    $csv = $fileObject->fgetcsv("\t");
                    if (empty($csv) || 1 === count($csv)) {
                        //fichier ou ligne vide
                        continue;
                    }
                    try {
                        if (0 === $index) {
                            //On passe l’entête.
                            $header = new Header($csv);
                            ++$index;
                            continue;
                        }
                        $columns = count($csv);
                        if (!in_array($columns, self::COLUMNS)) {
                            //On continue car on n'a pas le bon nombre de colonnes.
                            continue;
                        }
                        $passage = new Passage();
                        $passage
                            ->setCamera($camera)
                            ->setCoord($csv[$header->getColumn('coord')])
                            ->setCreated(new \DateTime(substr($csv[$header->getColumn('created')], 0, -3)))
                            ->setDataFictive(false)
                            ->setFiability($csv[$header->getColumn('fiability')])
                            ->setFile($fileEntity)
                            ->setH($csv[$header->getColumn('h')])
                            ->setImage($csv[$header->getColumn('image')])
                            ->setImmat($csv[$header->getColumn('plaque_court')])
                            ->setImmatriculation($csv[$header->getColumn('plaque_long')])
                            ->setIncrement($csv[$header->getColumn('increment')])
                            ->setL((int) $csv[$header->getColumn('nature_vehicule')])
                            ->setR((int) $csv[$header->getColumn('r')])
                            ->setS((int) $csv[$header->getColumn('s')])
                            ->setState($csv[$header->getColumn('pays')]);
                        if (isset($csv[$header->getColumn('plaque_collision')])) {
                            $passage->setImmatCollision($csv[$header->getColumn('plaque_collision')]);
                        } else {
                            $passage->setImmatCollision($csv[$header->getColumn('plaque_court')]);
                        }
                        $this->entityManager->persist($passage);
                    } catch (LoadException $e) {
                        $output->writeln(sprintf('<error>Erreur lors du chargement : %s</error> ', $e->getMessage()));
                        die();
                    }
                    if (0 === ($batchSize % $input->getOption('fetch'))) {
                        $this->entityManager->flush();
                        //$this->entityManager->clear();
                    }
                }
                //Fermeture du fichier
                unset($fileObject, $empreinte, $lignes);
                $this->entityManager->flush();
                //$this->entityManager->clear();
                $progressBar->finish();
                $output->writeln("\n");
                if ('f' === $input->getOption('transaction')) {
                    // Validation pour chaque fichier
                    $output->writeln('<info>Validation de la transaction.</info>');
                    $this->entityManager->commit();
                }
            }
            // Validation de l'ensemble du processus
            if ('f' !== $input->getOption('transaction') && 'n' === $input->getOption('transaction')) {
                $output->writeln('<info>Validation de la transaction.</info>');
                $this->entityManager->commit();
                //$this->entityManager->clear();
            }
            $output->writeln(sprintf('<comment>Mémoire consommée : %s</comment>', memory_get_usage()));
            //Fermeture du répertoire
            unset($directoryInfo, $directoryname);
        }
        $output->writeln('Fin du processus.');
        // if not released explicitly, Symfony releases the lock
        // automatically when the execution of the command ends
        $this->release();
        return 0;
    }
}
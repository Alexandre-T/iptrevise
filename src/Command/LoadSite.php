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
use App\Exception\LoadException;
use App\Entity\Site;
use App\Utils\Header;
use App\Utils\LoadUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
// use Symfony\Component\Console\Command\LockableTrait;
// use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class LoadSite extends Command
{
  // use LockableTrait;
  /**
  * Nombre de colonnes.
  */
  // const COLUMNS = [44, 45, 49, 50]; //On accepte 44 et 49, et on a ajouté une colonne
  // const BATCH_SIZE = 42000;
  /**
  * The entity manager.
  *
  * @var EntityManagerInterface
  */
  private $entityManager;
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
    // //Add an option for purge to purge database
    // ->addOption('purge', 'p', InputArgument::OPTIONAL, 'Répondre y si vous voulez purger les données en base (y/n)', 'n')
    //Add an option for reload file already loaded
    //->addOption('transaction', 't', InputArgument::OPTIONAL, 'Valider la transaction après chaque fichier, à la fin du processus ou sans transaction ? (f/p/n)', 'f')
    // //Add an option for reload file already loaded
    // ->addOption('memory_limit', 'm', InputArgument::OPTIONAL, 'Laisser PHP utiliser la quantité de mémoire souhaitée ? (y/n)', 'y')
    // //Add an option for reload file already loaded
    // ->addOption('fetch', 'f', InputArgument::OPTIONAL, 'Nombre de lignes entre chaque transmission à la base de données', self::BATCH_SIZE)
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
    // if (!$this->lock()) {
    //   $output->writeln('<error>The command is already running in another process.</error>');
    // }
    $sansErreur = true;
    $output->writeln([
      '<info>Loader lancé</info>',
      '<info>================</info>',
      '<info>Étape 1 : interrogation du fichier de sites...</info>',
      '<info>---------</info>'

    ]);

    $fd=fopen("Data/site.csv","r");
    $validator = Validation::createValidator();

    if (!$fd) {
      $output->writeln('Pas de fichier Data/sites.csv présent');
    } else {
      // if ('f' === $input->getOption('transaction')) {
      //   $this->entityManager->beginTransaction();
      // }
      $this->entityManager->beginTransaction();
      //Optimisation : on désactive le log des requêtes
      $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
      //Set memory limit.
      // if ('y' === $input->getOption('memory_limit')) {
      //   @ini_set('memory_limit', -1);
      // }

      while(!feof($fd)) {
        $Ligne = fgets($fd,255);
        $ligne = preg_split("/[\s,]+/", $Ligne); // ligne[0] label ligne[1] color
        $violations0 = $validator->validate($ligne[0], [new length(['max' => 32]),
        new NotBlank(),]);
        $violations1 = $validator->validate($ligne[1], [new regex(['pattern' => "/^([0-9a-f]{3}|[0-9a-f]{6})$/i",
         'message' => "form.site.error.color.pattern"]),
        new NotBlank(),]);
        if (0 !== count($violations0)) {
          $sansErreur = false;
          // there are errors, show them
          foreach ($violations0 as $violation0 ) {
            echo $violation0->getMessage().'<br>';
          }
        }
        if (0 !== count($violations1)) {
          $sansErreur = false;
          // there are errors, show them
          foreach ($violations1 as $violation1 ) {
            echo $violation1->getMessage().'<br>';
          }
        }

        $site = new Site();
        $site
        ->setLabel($ligne[0])
        ->setColor($ligne[1])
        ;
        $this->entityManager->persist($site);
      }


      //On débute une transaction
      // if ('f' !== $input->getOption('transaction') && 'n' !== $input->getOption('transaction')) {
      //   $this->entityManager->beginTransaction();
      // }


      //$lignes = $this->loader->getLines($fd);
      //$output->writeln(sprintf('<comment>Fichier %s en cours de chargement (%d lignes).</comment> ', $fileInfo->getFilename(), $lignes));
      // creates a new progress bar (50 units)
      //https://symfony.com/doc/current/components/console/helpers/progressbar.html
      //$progressBar = new ProgressBar($output, $lignes);
      // starts and displays the progress bar
      // $progressBar->start();
      // $progressBar->setFormat('debug');
      // $batchSize = 0;
      // while ($lignes > 0 && !$fileObject->eof()) {
      //   ++$batchSize;
      //   $progressBar->advance();
      //   $csv = $fileObject->fgetcsv("\t");
      //   if (empty($csv) || 1 === count($csv)) {
      //     //fichier ou ligne vide
      //     continue;
      //   }
      //   try {
      //     if (0 === $index) {
      //       //On passe l’entête.
      //       $header = new Header($csv);
      //       ++$index;
      //       continue;
      //     }
      //     $columns = count($csv);
      //     if (!in_array($columns, self::COLUMNS)) {
      //       //On continue car on n'a pas le bon nombre de colonnes.
      //       continue;
      //     }
      //   $site = new Site();
      //   $site
      //   ->setLabel($csv[$header->getColumn('label')])
      //   ->setColor($csv[$header->getColumn('color')])
      //   ;
      //   $this->entityManager->persist($site);
      // } catch (LoadException $e) {
      //   $output->writeln(sprintf('<error>Erreur lors du chargement : %s</error> ', $e->getMessage()));
      //   $sansErreur = false;
      //   //die();
      // }
      // if (0 === ($batchSize % $input->getOption('fetch'))) {
      //   $this->entityManager->flush();
      //   //$this->entityManager->clear();
      // }
    }
    //Fermeture du fichier
    //unset($fileObject, $empreinte, $lignes);
    $this->entityManager->flush();
    //$this->entityManager->clear();
    //$progressBar->finish();
    $output->writeln("\n");
    // if ('f' === $input->getOption('transaction')) {
    //   // Validation pour chaque fichier
    //   $output->writeln('<info>Validation de la transaction.</info>');
    //   if ($sansErreur) {
    //     $this->entityManager->commit();
    //   } else {
    //     $this->entityManager->rollback();
    //   }
    // }
    //}
    // Validation de l'ensemble du processus
    // if ('f' !== $input->getOption('transaction') && 'n' === $input->getOption('transaction')) {
    //   $output->writeln('<info>Validation de la transaction.</info>');
    //   if ($sansErreur) {
    //     $this->entityManager->commit();
    //   } else {
    //     $this->entityManager->rollback();
    //   }
    //   //$this->entityManager->clear();
    // }
    $output->writeln('<info>Validation de la transaction.</info>');
    if ($sansErreur) {
      $this->entityManager->commit();
      $output->writeln('<info>Transaction validé.</info>');
    } else {
      $this->entityManager->rollback();
      $output->writeln('<info>Transaction non validé.</info>');
    }
    $output->writeln(sprintf('<comment>Mémoire consommée : %s</comment>', memory_get_usage()));
    //Fermeture du répertoire
    //unset($directoryInfo, $directoryname);
    //}
    $output->writeln('Fin du processus.');
    // if not released explicitly, Symfony releases the lock
    // automatically when the execution of the command ends
    fclose($fd);
    // $this->release();
    return 0;
  }
}

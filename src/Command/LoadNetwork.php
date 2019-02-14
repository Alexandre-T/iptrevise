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
use App\Entity\Network;
use App\Entity\Site;
use App\Utils\Header;
use App\Utils\LoadUtils;
use App\Manager\SiteManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;


class LoadNetwork extends Command
{
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
    ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.')
    ;
  }

  /**
  * Shows the potential error messages.
  *
  * @param ConstraintViolationList
  * @param nLigne
  * @param OutputInterface $output
  *
  * @return bool
  *
  */
  function errorMessage( $violations, $n, $output): bool
  {
    $sansErreur = true;
    if (0 !== count($violations)) {
      $sansErreur = false;
      // there are errors, show them
      foreach ($violations as $violation ) {
        $output->writeln(sprintf('<error>Erreur ligne %d : %s</error> ',
        $n,
        $violation->getMessage()));
      }
    }
    return $sansErreur;
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
    $output->writeln([
      '<info>Loader lancé</info>',
      '<info>================</info>',
      '<info>Étape 1 : interrogation du fichier de networks...</info>',
      '<info>---------</info>'

    ]);

    $fd=fopen("Data/network.csv","r");
    $validator = Validation::createValidator();
    $nligne = 1;
    if (!$fd) {
      $output->writeln('Pas de fichier Data/networks.csv présent');
    } else {
      $this->entityManager->beginTransaction();
      //Optimisation : on désactive le log des requêtes
      $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
      //setLabel setDescription setIp setCidr setColor setSite
      while(!feof($fd)) {
        $Ligne = fgets($fd,255);
        $ligne = preg_split("/,/", $Ligne); // [0]: label [1]: ip
        // [2]: cidr [3]: color [4]: site [5]: description
        if ($ligne[0] !== "") {
          $violations0 = $validator->validate($ligne[0], [new length(['max' => 32]),
          new NotBlank(),]);
          $violations1 = $validator->validate($ligne[1], [new NotBlank(),]);
          $violations2 = $validator->validate(intval($ligne[2]), [new Range([
            'min' => 0,
            'max' => 32,
            'minMessage'=>"form.network.error.cidr.min",
            'maxMessage'=>"form.network.error.cidr.max"
          ]), new NotBlank(),]);
          $violations3 = $validator->validate($ligne[3], [new regex([
            'pattern' => "/^([0-9a-f]{3}|[0-9a-f]{6})$/i",
            'message' => 'form.network.error.color.pattern' //message dans validator.fr.yml
          ]), new NotBlank(),]);
          $sansErreur = ($this->errorMessage($violations0, $nligne, $output)
          && $this->errorMessage($violations1, $nligne, $output)
          && $this->errorMessage($violations2, $nligne, $output)
          && $this->errorMessage($violations3, $nligne, $output))
          && $sansErreur;
          $siteManager = new SiteManager($this->entityManager);
          $all = $siteManager->getAll();

          if ($sansErreur) {
            $network = new network();
            $network
            ->setLabel($ligne[0])
            ->setIp(ip2long($ligne[1]))
            ->setCidr(intval($ligne[2]))
            ->setColor($ligne[3])
            ->setDescription($ligne[5])
            ;
            foreach ($all as $site) {
              if ($site->getLabel() === $ligne[4]) {
                $network->setSite($site);
              }
            }
            $this->entityManager->persist($network);
          }
          $nligne++;
        }
      }
      $this->entityManager->flush();

      $output->writeln("\n");

      $output->writeln('<info>Validation de la transaction.</info>');
      if ($sansErreur) {
        $this->entityManager->commit();
        $output->writeln('<info>Transaction validé.</info>');
      } else {
        $this->entityManager->rollback();
        $output->writeln('<info>Transaction non validé.</info>');
      }
      $output->writeln(sprintf('<comment>Mémoire consommée : %s</comment>', memory_get_usage()));

      $output->writeln('Fin du processus.');

      //Closing file
      fclose($fd);

      return 0;
    }
  }
}

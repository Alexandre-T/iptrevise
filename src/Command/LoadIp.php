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
use App\Entity\Ip;
use App\Entity\Machine;
use App\Entity\Network;
use App\Utils\Header;
use App\Utils\LoadUtils;
use App\Manager\NetworkManager;
use App\Manager\MachineManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class LoadIp extends Command
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
    ->setName('app:load:ip')
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
      '<info>Étape 1 : interrogation du fichier de ips...</info>',
      '<info>---------</info>'

    ]);

    $fd=fopen("Data/ip.csv","r");
    $validator = Validation::createValidator();
    $nligne = 1;
    if (!$fd) {
      $output->writeln('Pas de fichier Data/ips.csv présent');
    } else {
      $this->entityManager->beginTransaction();
      //Optimisation : on désactive le log des requêtes
      $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);

      while(!feof($fd)) {
        $Ligne = fgets($fd,255);
        $ligne = preg_split("/,/", $Ligne);
        //[0]:ip [1]:network [2]:machine [3]:reason
        if ($ligne[0] !== "") {
          $violations0 = $validator->validate($ligne[0],
          [new NotBlank(),]);
          $violations3 = $validator->validate($ligne[3],
          [new length(['max' => 32])]);
          $sansErreur = ($this->errorMessage($violations0, $nligne, $output)
          && $this->errorMessage($violations3, $nligne, $output)
          && $sansErreur);

          $networkManager = new NetworkManager($this->entityManager);
          $allnet = $networkManager->getAll();
          $machineManager = new MachineManager($this->entityManager);
          $allmach = $machineManager->getAll();

          if ($sansErreur) {
            $ip = new ip();
            $ip
            ->setIp(ip2long($ligne[0]))
            ->setReason($ligne[3])
            ;
            foreach ($allnet as $network) {
              if ($network->getLabel() === $ligne[1]) {
                $ip->setNetwork($network);
              }
            }
            foreach ($allmach as $machine) {
              if ($machine->getLabel() === $ligne[2]) {
                $ip->setMachine($machine);
              }
            }
            $this->entityManager->persist($ip);
            $nligne++;
          }
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

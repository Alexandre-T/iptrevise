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
use App\Entity\Machine;
use App\Entity\Service;
use App\Utils\Header;
use App\Utils\LoadUtils;
use App\Manager\ServiceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;


class LoadMachine extends Command
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
    ->setName('app:load:machine')
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
      '<info>Étape 1 : interrogation du fichier de machines...</info>',
      '<info>---------</info>'

    ]);

    $fd=fopen("Data/machine.csv","r");
    $validator = Validation::createValidator();
    $nligne = 1;
    if (!$fd) {
      $output->writeln('Pas de fichier Data/machines.csv présent');
    } else {
      $this->entityManager->beginTransaction();
      //Optimisation : on désactive le log des requêtes
      $this->entityManager->getConnection()->getConfiguration()->setSQLLogger(null);
      while(!feof($fd)) {
        $Ligne = fgets($fd,255);
        $ligne = preg_split("/,/", $Ligne);
        //[0]:label [1]:inteface [2]:macs [3]:location [4]:description
        //[5]:services
        //WIP:
        //Peut-etre une liste de tags en septieme element
        //A demander au client
        if ($ligne[0] !== "") {
          $violations0 = $validator->validate($ligne[0], [new length(['max' => 32]),
          new NotBlank(),]);
          $violations1 = $validator->validate(intval($ligne[1]),
          [new GreaterThanOrEqual(['value'=>"0",
          'message'=>"form.machine.error.interface.min"]),
          new NotBlank(),]);
          $sansErreur = $this->errorMessage($violations0, $nligne, $output)
          && $this->errorMessage($violations1, $nligne, $output)
          && $sansErreur;
          $serviceManager = new ServiceManager($this->entityManager);
          $all = $serviceManager->getAll();
          if ($sansErreur) {
            $machine = new machine();
            $machine
            ->setLabel($ligne[0])
            ->setInterface(intval($ligne[1]))
            ->setMacs($ligne[2])
            ->setLocation($ligne[3])
            ->setDescription($ligne[4])
            ;
            $services = preg_split("/;/", $ligne[5]);
            foreach ($services as $service) {
              $found = false;
              foreach ($all as $al) {
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
              }
            }
            $this->entityManager->persist($machine);
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

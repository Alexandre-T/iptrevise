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
use App\Entity\Site;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
* LoadSite class.
*
* Load site from a csv file via a php command.
*
* Example: php bin/console app:load:site
*/
class LoadSite extends AbstractLoader
{
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
    // the full command description shown when running the command with
    // the "--help" option
    ->setHelp('Cette commande charge en base le contenu des fichiers téléchargés.')
    ;
  }

  /**
  * Transform line into entity and save it.
  *
  * @param array $ligne
  * @return ConstraintViolationList
  */
  function validateEntity(array $ligne): ConstraintViolationList
  {
    $validator = Validation::createValidator();
    $violations = new ConstraintViolationList();
    $violations->addAll(
      $validator->validate($ligne[0],
      [
        new Length(['max' => 32]),
        new NotBlank(),
      ]
      )
    );
    $violations->addAll(
      $validator->validate(
        $ligne[1],
        [
          new regex([
            'pattern' => '/^([0-9a-f]{3}|[0-9a-f]{6})$/i',
            'message' => 'form.site.error.color.pattern'
          ]), //message dans validator.fr.yml
          new NotBlank(),
        ]
        )
      );

      return $violations;
    }

    /**
    * Transform line into entity and save it.
    *
    * @param array $ligne
    * @return InformationInterface
    */
    function loadEntity(array $ligne): InformationInterface
    {
      $site = new Site();
      $site->setColor($ligne[1]);
      $site->setLabel($ligne[0]);

      return $site;
    }

    /**
    * Return the name of the file (site, ip, machine, etc.).
    *
    * @return string
    */
    function getFilename(): string
    {
      return __DIR__ . '/../../data/site.csv';
    }
  }

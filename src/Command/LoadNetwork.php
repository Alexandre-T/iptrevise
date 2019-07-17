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
use Symfony\Component\Validator\Constraints\IsFalse;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\NotNull;

class LoadNetwork extends AbstractLoader
{
    function validateEntity(array $ligne): ConstraintViolationList
    {
        $siteRepository = $this->entityManager->getRepository('App:Site');
        $networkRepository = $this->entityManager->getRepository('App:Network');
        $validator = Validation::createValidator();
        $violations = new ConstraintViolationList();
        $violations->addAll($validator->validate($ligne[4], [
            new NotBlank([
                'message' => sprintf('form.network.error.site.empty')
            ])
        ]));

        $violations->addAll($validator->validate($siteRepository->findOneBy(['label' => $ligne[4]]), [
            new NotNull([
                'message' => $this->translator->trans(
                    'form.network.error.site.exist %site%',
                    ['%site%' => $ligne[4]],
                    'validators'
                )
                //'message' => 'form.network.error.site.exist %site%', ['%site%' => $ligne[4]]
            ])
        ]));

        $violations->addAll($validator->validate($ligne[0], [
            new Length([
                'min' => 1,
                'max' => 32,
                'minMessage' => 'form.network.error.label.min-length',
                'maxMessage' => 'form.network.error.label.max-length'
            ]),
            new NotBlank([
                'message' => 'form.network.error.label.not-blank'
            ])
        ]));

        $test = $networkRepository->findOneBy(['label' => $ligne[0]]);
        $violations->addAll($validator->validate($test instanceof Network, [
            new IsFalse([
                'message' => 'form.network.error.label.unique'
            ])
        ]));

        $violations->addAll($validator->validate($ligne[1], [
            new Regex([
                'pattern' => '/^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i',
                'message' => $this->translator->trans(
                    'form.network.error.ip.pattern %ip%',
                    ['%ip%' => $ligne[1]],
                    'validators')
            ]),
            new NotBlank()]));
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

//        if (!$site instanceof Site) {
//            throw new \Exception($this->translator->trans('command.error.site.not-exists %label%,', [
//                '%label%' => $ligne[4],
//            ]));
//        }

        $network = new network();
        $network
            ->setLabel($ligne[0])
            ->setIp(ip2long($ligne[1]))
            ->setCidr(intval($ligne[2]))
            ->setColor($ligne[3])
            ->setSite($site)
            ->setDescription($ligne[5]);
        return $network;
    }

    function getFilename(): string
    {
        return __DIR__ . '/../../data/network.csv';
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
}

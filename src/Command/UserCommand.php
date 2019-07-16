<?php
/**
 * This file is part of the ip-manager Application.
 *
 * PHP version 7.2
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2019 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Command;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCommand extends Command
{
    protected static $defaultName = 'app:user';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * UserCommand constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct();
        $this->objectManager = $objectManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Create a new user')
            ->addArgument('label', InputArgument::REQUIRED, 'User label')
            ->addArgument('mail', InputArgument::REQUIRED, 'User mail')
            ->addArgument('password', InputArgument::OPTIONAL, 'User password')
            ->addOption('admin', null, InputOption::VALUE_NONE, 'If option is set, user will be promote to administrator rank')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $io->note('Process launched...');
        $mail = $input->getArgument('mail');
        $label = $input->getArgument('label');
        $password = $input->getArgument('password');

        $user = new User();
        $user->setMail($mail);
        $user->setLabel($label);

        if (!empty($password)) {
            $user->setPlainPassword($password);
        }

        if (!empty($input->getOption('admin'))) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->objectManager->persist($user);
        $this->objectManager->flush();

        $io->success('User created.');
    }
}

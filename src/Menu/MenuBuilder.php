<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 *
 */
namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class MenuBuilder
{
    /**
     * Facotry Interface.
     *
     * @var FactoryInterface
     */
    private $factory;

    /**
     * Authorization Checker.
     *
     * @var AuthorizationChecker
     */
    private $authorization;

    /**
     * Token.
     *
     * @var TokenInterface
     */
    private $token;

    /**
     * The User or similar.
     *
     * @var mixed Can be a UserInterface instance, an object implementing a __toString method,
     *               or the username as a regular string
     */
    private $user;

    /**
     * MenuBuilder constructor.
     *
     * @constructor
     *
     * @param FactoryInterface $factory
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage $tokenStorage
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->factory = $factory;
        $this->authorization = $authorizationChecker;
        $this->token = $tokenStorage->getToken();
        $this->user = $this->token->getUser();
    }

    /**
     * Main menu for ROLE_USER.
     *
     * @param array $options
     * @return ItemInterface
     */
    public function createMainMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('menu.main.home', [
            'route' => 'home',
            'icon'  => 'fw fa-home',
        ]);

        if ($this->authorization->isGranted('ROLE_USER'))
        {
            $dropdownSettings = $menu->addChild('menu.main.networks', [
                'icon' => 'fw fa-network',
                'pull-right' => true,
                'dropdown' => true,
                'caret' => true,
            ]);

            $dropdownSettings->addChild('menu.main.network', [
                'icon' => 'fw fa-network',
                'route' => 'home',
            ]);

            $dropdownSettings->addChild('menu.main.machine', [
                'icon' => 'fw fa-computer',
                'route' => 'home',
            ]);

            $dropdownSettings->addChild('menu.main.ip', [
                'icon' => 'fw fa-globe',
                'route' => 'home',
            ]);
        }

        // ... add more children

        return $menu;
    }

    /**
     * Menu to login or logout
     *
     * @param array $options
     * @return ItemInterface
     */
    public function createUserMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $isFully = $this->authorization->isGranted('IS_AUTHENTICATED_FULLY');
        $isRemembered = $this->authorization->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $isAnonymous = $this->authorization->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');

        if ($isFully || $isRemembered) {
            //@FIXME vérifier le type d'User.
            $dropdownUser = $menu->addChild($this->user->getUsername(), [
                'icon' =>'user',
                'pull-right' => true,
                'dropdown' => true,
                'caret' => true,
            ])->setExtra('translation_domain', false);
            $dropdownUser->addChild('menu.user.your-profile', [
                'dropdown-header' => true
            ]);
            $dropdownUser->addChild('menu.user.show-profile', [
                'icon' => 'fw fa-eye',
                'route' => 'home'
            ]);
            $dropdownUser->addChild('menu.user.edit-profile', [
                'icon' => 'fw fa-pencil',
                'route' => 'home'
            ]);
            //Adding a nice divider
            $dropdownUser->addChild('divider_1', ['divider' => true])
                ->setExtra('translation_domain', false);
            //Adding LOGOUT
            $dropdownUser->addChild('menu.user.logout', [
                'icon' => 'fw fa-sign-out',
                'route' => 'home'
            ]);
        } elseif ($isAnonymous) {
            $menu->addChild('menu.user.sign-in', [
                'icon' => 'fw fa-sign-in',
                'route' => 'home'
            ]);
            $menu->addChild('menu.user.sign-up', [
                'icon' => 'fw fa-pencil-square-o',
                'route' => 'home'
            ]);
        }

        return $menu;
    }

    /**
     * Menu for Admin user
     *
     * @param array $options
     * @return ItemInterface
     */
    public function createAdminMenu(array $options)
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('Home', ['route' => 'home']);
        // ... add more children

        return $menu;
    }
}

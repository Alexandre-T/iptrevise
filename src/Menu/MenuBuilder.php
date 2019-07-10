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
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class MenuBuilder
{
    /**
     * Factory Interface.
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
     *            or the username as a regular string
     */
    private $user;

    /**
     * MenuBuilder constructor.
     *
     * @constructor
     *
     * @param FactoryInterface     $factory
     * @param AuthorizationChecker $authorizationChecker
     * @param TokenStorage         $tokenStorage
     */
    public function __construct(FactoryInterface $factory, AuthorizationChecker $authorizationChecker, TokenStorage $tokenStorage)
    {
        $this->factory = $factory;
        $this->authorization = $authorizationChecker;
        $this->token = $tokenStorage->getToken();

        if ($this->token instanceof TokenInterface) {
            $this->user = $this->token->getUser();
        }
    }

    /**
     * Main menu for ROLE_USER.
     *
     * NB: an array $options could be injected by  MopaBundle.
     * you only have to add array $options as a parameter, it will be populated by MopaBundle and DI.
     *
     * @return ItemInterface
     */
    public function createMainMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('menu.main.home', [
            'route' => 'home',
            'icon' => 'fw fa-home',
        ]);

        if ($this->user instanceof User && ($this->user->isAdmin() or count($this->user->getNewRoles()))) {
            $dropdownSettings = $menu->addChild('menu.main.listings', [
                'icon' => 'fas fa-list',
                'pull-right' => true,
                'dropdown' => true,
                'caret' => true,
            ]);

            $dropdownSettings->addChild('menu.main.machines', [
                'icon' => 'fw fa-desktop',
                'route' => 'default_machine_index',
            ]);
            $dropdownSettings->addChild('menu.main.networks', [
                'icon' => 'fw fa-sitemap',
                'route' => 'default_network_index',
            ]);

            if ( $this->authorization->isGranted('IS_AUTHENTICATED_FULLY') )
            {
              $dropdownSettings->addChild('menu.main.services', [
                'icon' => 'fw fa-stack-overflow',
                'route' => 'default_service_index',
              ]);
            }

            $dropdownSettings->addChild('menu.main.site', [
                'icon' => 'fw fa-building-o',
                'route' => 'default_site_index',
            ]);
        }

        // ... add more children

        return $menu;
    }

    /**
     * Menu to login or logout.
     *
     * @return ItemInterface
     */
    public function createUserMenu()
    {
        $menu = $this->factory->createItem('root');

        $isFully = $this->authorization->isGranted('IS_AUTHENTICATED_FULLY');
        $isRemembered = $this->authorization->isGranted('IS_AUTHENTICATED_REMEMBERED');
        $isAnonymous = $this->authorization->isGranted('IS_AUTHENTICATED_ANONYMOUSLY');

        if ($isFully || $isRemembered) {
            $dropdownUser = $menu->addChild($this->getUsername(), [
                'icon' => 'user',
                'pull-right' => true,
                'dropdown' => true,
                'caret' => true,
            ])->setExtra('translation_domain', false);
            $dropdownUser->addChild('menu.user.your-profile', [
                'dropdown-header' => true,
            ]);
            $dropdownUser->addChild('menu.user.show-profile', [
                'icon' => 'fw fa-eye',
                'route' => 'home',
            ]);
            $dropdownUser->addChild('menu.user.edit-profile', [
                'icon' => 'fw fa-pencil',
                'route' => 'home',
            ]);
            //Adding a nice divider
            $dropdownUser->addChild('divider_1', ['divider' => true])
                ->setExtra('translation_domain', false);
            //Adding LOGOUT
            $dropdownUser->addChild('menu.user.logout', [
                'icon' => 'fw fa-sign-out',
                'route' => 'security_logout',
            ]);
        } elseif ($isAnonymous) {
            $menu->addChild('menu.user.sign-in', [
                'icon' => 'fw fa-sign-in',
                'route' => 'security_login',
            ]);
        }

        return $menu;
    }

    /**
     * Menu for Admin user.
     *
     * @return ItemInterface
     */
    public function createAdminMenu()
    {
        $menu = $this->factory->createItem('root');

        if ($this->authorization->isGranted('ROLE_ADMIN')) {
            $dropdownAdmin = $menu->addChild('menu.admin.admin', [
                'icon' => 'user',
                'pull-right' => true,
                'dropdown' => true,
                'caret' => true,
            ]);

            $dropdownAdmin->addChild('menu.admin.users', [
                'icon' => 'fw fa-group',
                'route' => 'administration_user_index',
            ]);
            $dropdownAdmin->addChild('divider_1', ['divider' => true])
                ->setExtra('translation_domain', false);
            $dropdownAdmin->addChild('default.entity.deleted', [
                'dropdown-header' => true,
            ]);
            $dropdownAdmin->addChild('default.site.deleted', [
                    'icon' => 'fw fa-building-o',
                    'route' => 'default_deleted_site_index',
            ]);
            $dropdownAdmin->addChild('default.network.deleted', [
                    'icon' => 'fw fa-sitemap',
                    'route' => 'default_deleted_network_index',
            ]);
            $dropdownAdmin->addChild('default.machine.deleted', [
                    'icon' => 'fw fa-desktop',
                    'route' => 'default_deleted_machine_index',
            ]);
            $dropdownAdmin->addChild('default.ip.deleted', [
                    'icon' => 'fw fa-indent',
                    'route' => 'default_deleted_ip_index',
            ]);

            // ... add more children
        }

        return $menu;
    }

    /**
     * Return the username.
     *
     * @return string
     */
    private function getUsername(): string
    {
        if ($this->user instanceof User) {
            $username = $this->user->getLabel();
        } elseif (null === $this->user) {
            $username = 'menu.user.unknown';
        } else {
            $username = (string) ($this->user);
        }

        return $username;
    }
}

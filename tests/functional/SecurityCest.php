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

/**
 * SecurityCest class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class SecurityCest
{
    /**
     * Test to connect as Reader.
     *
     * @param FunctionalTester $I
     */
    public function tryToConnectAsReader(FunctionalTester $I)
    {
        $I->connect('reader@example.org', 'reader');

        $I->seeCurrentUrlEquals('/');

        $I->see('Accueil', '.active');

        $I->see('Reader', '.navbar');
        $I->see('Consultation', '.navbar');
        $I->see('Édition', '.navbar');

        $I->see('Réseaux', '.navbar');
        $I->see('Machines', '.navbar');
        $I->see('Adresses IP', '.navbar');

        $I->click('Déconnexion');
        $I->seeCurrentUrlEquals('/');
        $I->see('Connexion', '.navbar');
    }
    /**
     * Test to connect as User.
     *
     * @param FunctionalTester $I
     */
    public function tryToConnectAsUser(FunctionalTester $I)
    {
        $I->connect('user@example.org', 'user');

        $I->seeCurrentUrlEquals('/');

        $I->see('Accueil', '.active');

        $I->see('User', '.navbar');
        $I->see('Consultation', '.navbar');
        $I->see('Édition', '.navbar');

        $I->dontSee('Réseaux', '.navbar');
        $I->dontSee('Machines', '.navbar');
        $I->dontSee('Adresses IP', '.navbar');

        $I->dontSee('Gestion des utilisateurs', '.navbar');

        $I->click('Déconnexion');
        $I->seeCurrentUrlEquals('/');
        $I->see('Connexion', '.navbar');
    }
    /**
     * Test to connect as Admin.
     *
     * @param FunctionalTester $I
     */
    public function tryToConnectAsAdmin(FunctionalTester $I)
    {
        $I->connect('administrator@example.org', 'administrator');

        $I->seeCurrentUrlEquals('/');

        $I->see('Accueil', '.active');

        $I->see('Admin', '.navbar');
        $I->see('Consultation', '.navbar');
        $I->see('Édition', '.navbar');

        $I->see('Réseaux', '.navbar');
        $I->see('Machines', '.navbar');
        $I->see('Adresses IP', '.navbar');
        
        $I->see('Gestion des utilisateurs', '.navbar');

        $I->click('Déconnexion');
        $I->seeCurrentUrlEquals('/');
        $I->see('Connexion', '.navbar');
    }
    /**
     * Test to connect as Organiser.
     *
     * @param FunctionalTester $I
     */
    public function tryToConnectAsOrganiser(FunctionalTester $I)
    {
        $I->connect('organiser@example.org', 'organiser');

        $I->seeCurrentUrlEquals('/');

        $I->see('Accueil', '.active');

        $I->see('Organiser', '.navbar');
        $I->see('Consultation', '.navbar');
        $I->see('Édition', '.navbar');

        $I->see('Réseaux', '.navbar');
        $I->see('Machines', '.navbar');
        $I->see('Adresses IP', '.navbar');

        $I->dontSee('Gestion des utilisateurs', '.navbar');

        $I->click('Déconnexion');
        $I->seeCurrentUrlEquals('/');
        $I->see('Connexion', '.navbar');
    }
}

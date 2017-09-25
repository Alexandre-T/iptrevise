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
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

/**
 * SecurityCest class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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

        $I->wantToTest('Reader privileges');
        $I->amOnPage('/machine/');
        $I->dontSeeLink('Nouvelle machine');
        $I->dontSeeLink('Éditer');

        $I->click('Consulter','tr.row-2');
        $I->dontSeeLink('Dissocier');
        $I->dontSeeLink(' Éditer');
        $I->dontSeeLink('Modifier');
        $I->dontSeeLink('Supprimer');
        $I->dontSeeLink('Supprimer cette adresse IP');

        $I->amOnPage('/network/');
        $I->dontSeeLink('Nouveau réseau');
        $I->dontSeeLink('Éditer');

        $I->click('Consulter','tr.row-3');
        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/network/$id");

        $I->dontSeeLink('Associer');
        $I->dontSeeLink('Associer à une nouvelle machine');
        $I->dontSeeLink('Dissocier');
        $I->dontSeeLink(' Éditer');
        $I->dontSeeLink('Modifier');
        $I->dontSeeLink('Supprimer');
        $I->dontSeeLink('Supprimer cette adresse IP');

        $I->click('192.168.10.29');
        $I->dontSeeLink(' Éditer');
        $I->dontSeeLink(' Supprimer');


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

        $I->dontSee('Gestion des utilisateurs', '.navbar');

        $I->wantToTest('Organiser privileges');
        $I->amOnPage('/machine');
        $I->seeLink('Nouvelle machine');
        $I->seeLink('Éditer');

        $I->click('Consulter','tr.row-2');
        $I->seeLink('Dissocier');
        $I->seeLink(' Éditer');
        $I->seeLink('Modifier');
        $I->seeLink('Supprimer');
        $I->seeLink('Supprimer cette adresse IP');

        $I->amOnPage('/network');
        $I->seeLink('Nouveau réseau');
        $I->seeLink('Éditer');

        $I->click('Consulter','tr.row-3');
        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/network/$id");

        $I->seeLink('Associer');
        $I->seeLink('Associer une nouvelle machine');
        $I->seeLink('Dissocier');
        $I->seeLink(' Éditer');
        $I->seeLink('Modifier');
        $I->seeLink('Supprimer');
        $I->seeLink('Supprimer cette adresse IP');

        $I->click('192.168.10.29');
        $I->seeLink(' Éditer');
        $I->see(' Supprimer','button');

        $I->click('Déconnexion');
        $I->seeCurrentUrlEquals('/');
        $I->see('Connexion', '.navbar');
    }
}

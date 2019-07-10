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
 * Deleted class.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 */
class DeletedCest
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

        $this->seeAllAccessDenied($I);
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

        $this->seeAllAccessDenied($I);
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

        $I->click('Sites supprimés');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Site banni');
        $I->click('Consulter');
        $I->seeResponseCodeIsSuccessful();

        $I->click('Machines supprimées');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Machine bannie');
        $I->click('Consulter');
        $I->seeResponseCodeIsSuccessful();

        $I->click('Réseaux supprimés');
        $I->seeResponseCodeIsSuccessful();
        $I->see('Réseau banni');
        $I->click('Consulter');
        $I->seeResponseCodeIsSuccessful();

        $I->click('IP supprimées');
        $I->seeResponseCodeIsSuccessful();
        $I->see('192.168.15.45');
        $I->click('Consulter');
        $I->seeResponseCodeIsSuccessful();

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

        $this->seeAllAccessDenied($I);
    }

    /**
     * Test that all pages are denied.
     *
     * @param FunctionalTester $I
     */
    private function seeAllAccessDenied(FunctionalTester $I)
    {
        $I->amOnPage('/ip/deleted');
        $I->seeResponseCodeIs(403);
    }
}

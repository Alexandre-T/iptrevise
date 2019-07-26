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
class SearchCest
{
    /**
     * Test to connect as Reader.
     *
     * @param FunctionalTester $I
     */
    public function tryToSearchAsReader(FunctionalTester $I)
    {
        $I->connect('reader1@example.org', 'reader');
        $I->seeCurrentUrlEquals('/');
        $I->fillField('search', '192.168.15.43');
        $I->click('GO');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/search/?search=192.168.15.43');
        $I->see('Résultat de la recherche');
        $I->seeLink('Machine 43');
        $I->seeLink('Network 15');
        $I->seeLink('Site noir');
        $I->seeLink('192.168.15.43');

        //This IP does not exists
        $I->fillField('search', '192.168.15.44');
        $I->click('GO');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/search/?search=192.168.15.44');
        $I->see('Résultat de la recherche');
        $I->dontSeeLink('Machine 43');
        $I->dontSeeLink('Network 15');
        $I->dontSeeLink('Site noir');
        $I->dontSeeLink('192.168.15.43');
        $I->dontSeeLink('192.168.15.44');

        //Blank search
        $I->fillField('search', '');
        $I->click('GO');
        $I->seeResponseCodeIsSuccessful();
        $I->amOnPage('/search/?search=');
        $I->see('Résultat de la recherche');
        $I->seeLink('Machine 43');
        $I->seeLink('Network 15');
        $I->seeLink('Site noir');
        $I->seeLink('192.168.15.43');
        $I->seeLink('Machine 0');

    }
}

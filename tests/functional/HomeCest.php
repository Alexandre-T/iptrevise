<?php


class HomeCest
{
    public function _before(FunctionalTester $I)
    {
    }

    public function _after(FunctionalTester $I)
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
        $I->wantToTest('The Homepage is well displayed');
        $I->amOnPage('/');
        $I->see('IP-Trevise');

    }
}

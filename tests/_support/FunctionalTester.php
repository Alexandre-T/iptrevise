<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

    /**
     * Connect as $mail $password.
     *
     * @param string $mail
     * @param string $password
     */
    public function connect(string $mail, string $password)
    {
        $I = $this;

        $I->amOnPage('/login');
        $I->fillField('Adresse email', $mail);
        $I->fillField('Mot de passe', $password);
        $I->click(' Se connecter');
    }
}

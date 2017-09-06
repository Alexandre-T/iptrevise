<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Testing
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 *
 */

/**
 * User Crud Cest.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class UserCrudCest
{
    /**
     * I want to show all users
     *
     * @param FunctionalTester $I
     */
    public function crudUser(FunctionalTester $I)
    {
        $I->wantToTest('I cannot access user managment without login');
        $I->amOnPage('/administration/user');
        $I->canSeeCurrentUrlEquals('/login');

        $I->wantTo('be connected as an admin');
        $I->fillField('Adresse email', 'administrator@example.org');
        $I->fillField('Mot de passe', 'administrator');
        $I->click(' Se connecter');
        $I->canSeeCurrentUrlEquals('/administration/user');

        $I->wantToTest('Menu is well displayed');
        $I->see('Accueil');
        $I->dontSee('Accueil', '.active');
        $I->see('Déconnexion', '.navbar');
        $I->see('Gestion des utilisateurs', '.active');
        $I->see('Administration', '.active');

        $I->wantToTest('the user management page');
        $I->see('Organizer', 'td[headers=user-username]');
        $I->see('User', 'td[headers=user-username]');
        $I->see('Reader', 'td[headers=user-username]');
        $I->see('Administrator', 'td[headers=user-username]');

        $I->wantTo('See the new user form');
        $I->click('Nouvel utilisateur');
        $I->canSeeCurrentUrlEquals('/administration/user/new');
        $I->wantToTest('The required field');
        $I->click('Créer');
        $I->canSeeCurrentUrlEquals('/administration/user/new');

        //@TODO Je dois le voir deux fois !
        $I->see('Cette valeur ne doit pas être vide.', '.help-block');
        $I->see('Vous devez sélectionner au moins un rôle.', '.help-block');

        $I->wantToTest('The email address');
        $I->fillField('Adresse mail', 'foo is not valid email');
        $I->click('Créer');
        $I->canSeeCurrentUrlEquals('/administration/user/new');
        $I->see('Cette valeur n\'est pas une adresse email valide.', '.help-block');

        $I->wantToTest('that I cannot create Doublon');
        $I->fillField('Adresse mail', 'reader@example.org');
        $I->fillField('Identifiant', 'valable');
        $I->click('Créer');
        $I->canSeeCurrentUrlEquals('/administration/user/new');
        $I->see('Cette adresse mail est déjà utilisée.', '.help-block');

        $I->fillField('Adresse mail', 'valable@example.org');
        $I->fillField('Identifiant', 'Reader');
        $I->click('Créer');
        $I->canSeeCurrentUrlEquals('/administration/user/new');
        $I->see('Cet identifiant est déjà utilisé.', '.help-block');

        $I->wantToTest('The length of each field');
        $tooLong = str_repeat('a', 255);
        $I->fillField('Adresse mail', $tooLong.'@toto.fr');
        $I->click('Créer');
        $I->canSeeCurrentUrlEquals('/administration/user/new');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.', '.help-block');
        $I->fillField('Adresse mail', 'codeception@test.org');
        $I->fillField('Identifiant', str_repeat('a', 37));
        $I->click('Créer');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.', '.help-block');

        $I->wantToTest('A valid form');
        $I->fillField('Adresse mail', 'codeception@test.org');
        $I->fillField('Identifiant', 'Codeception');
        $I->checkOption('Utilisateur Cerbère');
        $I->click('Créer');

        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->canSeeCurrentUrlEquals("/administration/user/$id");

        $I->wantToTest('the show Use Case');
        $I->see('L’utilisateur Codeception a été créé avec succès !', '.alert-success');

        $I->see('Informations générales');
        $I->see('Codeception', 'dd.lead');
        $I->see('codeception@test.org', 'dd');

        $I->see('Journal de bord');
        $I->see('1', 'td[headers="logs-version"]');
        $I->see('Création', 'td[headers="logs-action"]');
        $I->see('administrator@example.org', 'td[headers="logs-user"]');

        $I->see('Information');
        //@todo vérifier la date de création
        //@todo vérifier la date de modification

        $I->wantTo('Return to the list of users and see my creation');
        $I->click(' Liste des utilisateurs');
        $I->canSeeCurrentUrlEquals('/administrator/user');
        $I->see("codeception@test.org", 'td[headers="user-mail"]');
        $I->see("Codeception", 'td[headers="user-username"]');

        $I->wantTo('Edit my creation');
        $I->amOnPage("/administration/user/$id/edit");
        $I->canSeeCurrentUrlEquals("/administration/user/$id/edit");



    }
}

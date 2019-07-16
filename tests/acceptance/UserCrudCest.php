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
 * @copyright 2017 Cerema
 * @license   CeCILL-B V1
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

/**
 * User Crud Cest.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
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
        $I->seeCurrentUrlEquals('/login');

        $I->wantTo('be connected as an admin');
        $I->fillField('Adresse email', 'administrator@example.org');
        $I->fillField('Mot de passe', 'administrator');
        $I->click(' Se connecter');
        $I->seeCurrentUrlEquals('/administration/user');

        $I->wantToTest('Menu is well displayed');
        $I->see('Accueil');
        $I->dontSee('Accueil', '.active');
        $I->see('Déconnexion', '.navbar');
        $I->see('Gestion des utilisateurs', '.active');
        $I->see('Administration', '.active');

        $I->wantToTest('the user management page');
        $I->see('Organiser', 'td[headers=user-username]');
        $I->see('User', 'td[headers=user-username]');
        $I->see('Reader', 'td[headers=user-username]');
        $I->see('Administrator', 'td[headers=user-username]');

        $I->wantTo('See the new user form');
        $I->click('Nouvel utilisateur');
        $I->seeCurrentUrlEquals('/administration/user/new');
        $I->wantToTest('The required field');
        $I->uncheckOption('Utilisateur Cerbère');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/administration/user/new');

        //@TODO Je dois le voir deux fois !
        $I->see('Cette valeur ne doit pas être vide.', '.help-block');
        $I->see('Vous devez sélectionner au moins un rôle.', '.help-block');

        $I->wantToTest('The email address');
        $I->fillField('Adresse mail', 'foo is not valid email');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/administration/user/new');
        $I->see('Cette valeur n\'est pas une adresse email valide.', '.help-block');

        $I->wantToTest('that I cannot create Doublon');
        $I->fillField('Adresse mail', 'reader@example.org');
        $I->fillField('Identifiant', 'valable');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/administration/user/new');
        $I->see('Cette adresse mail est déjà utilisée.', '.help-block');

        $I->fillField('Adresse mail', 'valable@example.org');
        $I->fillField('Identifiant', 'Reader Sites');
        $I->checkOption('Utilisateur Cerbère');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/administration/user/new');
        $I->see('Cet identifiant est déjà utilisé.', '.help-block');

        $I->wantToTest('The length of each field');
        $tooLong = str_repeat('a', 255);
        $I->fillField('Adresse mail', $tooLong.'@toto.fr');
        $I->checkOption('Utilisateur Cerbère');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/administration/user/new');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 255 caractères.', '.help-block');
        $I->fillField('Adresse mail', 'codeception@test.org');
        $I->fillField('Identifiant', str_repeat('a', 37));
        $I->checkOption('Utilisateur Cerbère');
        $I->click('Créer');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.', '.help-block');

        $I->wantToTest('A valid form');
        $I->fillField('Adresse mail', 'codeception@test.org');
        $I->fillField('Identifiant', 'Codeception');
        $I->checkOption('Utilisateur Cerbère');
        $I->click('Créer');

        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/administration/user/$id");

        $I->wantToTest('the show Use Case');
        $I->see('L’utilisateur Codeception a été créé avec succès !', '.alert-success');

        $I->see('Informations générales');
        $I->see('Codeception', 'dd.lead');
        $I->see('codeception@test.org', 'dd');

        $I->see('Journal de bord');
        $I->see('1', 'td[headers="logs-version"].row1');
        $I->see('Création', 'td[headers="logs-action"].row1');
        $I->see('administrator@example.org', 'td[headers="logs-user"].row1');
        $I->see('Codeception', 'td[headers="logs-value"].row1');
        $I->see('codeception@test.org', 'td[headers="logs-value"].row1');
        $I->see('Aucun', 'td[headers="logs-value"].row1');
        $I->see('Utilisateur cerbérisé', 'td[headers="logs-value"].row1');

        $I->see('Information');
        //@todo vérifier la date de création
        //@todo vérifier la date de modification

        $I->wantTo('Return to the list of users and see my creation');
        $I->click(' Liste des utilisateurs');
        $I->seeCurrentUrlEquals('/administration/user');
        $I->see("codeception@test.org", 'td[headers="user-mail"]');
        $I->see("Codeception", 'td[headers="user-username"]');

        $I->wantTo('Edit my creation');
        //TODO Ajouter les tests sur les rôles.
        $I->amOnPage("/administration/user/$id/edit");
        $I->seeCurrentUrlEquals("/administration/user/$id/edit");

        $I->wantTo('Test that the form is well initialized');
        $I->seeInField('Identifiant', 'Codeception');
        $I->seeInField('Adresse mail', 'codeception@test.org');
        $I->seeCheckboxIsChecked('Utilisateur Cerbère');
        $I->dontSeeCheckboxIsChecked('Administrateur');

        $I->checkOption('Administrateur');
        $I->uncheckOption('Utilisateur Cerbère');
        $I->click('Éditer');

        $I->seeCurrentUrlEquals("/administration/user/$id");
        $I->see('Les modifications apportées à l’utilisateur Codeception ont été enregistrées avec succès', '.alert-success');
        $I->see('Administrateur, Utilisateur cerbérisé', '#administration-global-information dd');

        $I->see('Journal de bord');
        $I->see('2', 'td[headers="logs-version"].row2');
        $I->see('Modification', 'td[headers="logs-action"].row2');
        $I->see('administrator@example.org', 'td[headers="logs-user"].row2');
        $I->see('Administrateur', 'td[headers="logs-value"].row2');

        $I->wantTo('Delete my user');
        $I->click('Supprimer', '#form_delete');
        $I->seeCurrentUrlEquals("/administration/user");
        $I->see('L’utilisateur Codeception a été supprimé avec succès', '.alert-success');
        $I->dontSee('Codeception', 'td[headers="user-username"]' );

    }
}

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
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

/**
 * Service Crud Cest.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 */
class ServiceCrudCest
{
    /**
     * I want to show all services and manage them.
     *
     * @param FunctionalTester $I
     */
    public function crudService(FunctionalTester $I)
    {
        $I->wantToTest('I cannot access service managment without login');
        $I->amOnPage('/service');
        $I->seeCurrentUrlEquals('/login');

        $I->wantTo('be connected as an organiser');
        $I->connect('organiser@example.org', 'organiser');
        $I->seeCurrentUrlEquals('/service');

        $I->wantToTest('Menu is well displayed');
        $I->see('Accueil');
        $I->dontSee('Accueil', '.active');
        $I->see('Déconnexion', '.navbar');
        $I->see('Services', '.active');

        $I->wantToTest('the service management page');
        $I->see('Service 0', 'tr.row-4 td[headers="service-label"]');

        $I->wantTo('See the new service form');
        $I->click('Nouveau service');
        $I->seeCurrentUrlEquals('/service/new');
        $I->wantToTest('The required field');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/service/new');

        //@TODO Je dois le voir plusieurs fois ou personnaliser chaque message d'erreur !
        $I->see('Cette valeur ne doit pas être vide.', '.help-block');

        $I->wantToTest('that I cannot create a second service with the same name');
        $I->fillField('Service', 'Service 0');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/service/new');
        $I->see('Un service du même nom existe déjà', '.help-block');

        $I->wantToTest('The length of each field');
        $I->fillField('Service', str_repeat('a', 17));
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/service/new');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 16 caractères.', '.help-block');

        $I->wantToTest('A valid form');
        $I->fillField('Service', 'Service Codecept');
        $I->click('Créer');

        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/service/$id");

        $I->wantToTest('the show Use Case');
        $I->see('Service Codecept', '.alert-success');
        $I->see('a été créé avec succès', '.alert-success');

        $I->see('Informations générales');
        $I->see('Service Codecept', 'dd.lead');

        $I->see('Journal de bord');
        $I->see('1', 'td[headers="logs-version"].row1');
        $I->see('Création', 'td[headers="logs-action"].row1');
        $I->see('organiser@example.org', 'td[headers="logs-user"].row1');
        $I->see('Service Codecept', 'td[headers="logs-value"].row1');

        $I->see('Information');
        //@todo vérifier la date de création
        //@todo vérifier la date de modification

        $I->wantTo('Return to the list of service and see my creation');
        $I->click(' Liste des services');
        $I->seeCurrentUrlEquals('/service');
        $I->see('Service Codecept', 'tr.row-5 td[headers="service-label"]');

        $I->wantTo('Edit my creation');
        $I->amOnPage("/service/$id/edit");
        $I->seeCurrentUrlEquals("/service/$id/edit");

        $I->wantTo('Test that the form is well initialized');
        $I->seeInField('Service', 'Service Codecept');

        $I->fillField('Service', 'Serv. Codecept');

        $I->click('Éditer');

        $I->seeCurrentUrlEquals("/service/$id");
        $I->see('Serv. Codecept', '.alert-success');
        $I->see('a été modifié avec succès', '.alert-success');

        $I->see('Journal de bord');
        $I->see('2', 'td[headers="logs-version"].row2');
        $I->see('Modification', 'td[headers="logs-action"].row2');
        $I->see('organiser@example.org', 'td[headers="logs-user"].row2');
        $I->dontSee('Service Codecept', 'td[headers="logs-value"].row2');

        $I->wantTo('Delete my service');
        $I->click('Supprimer', '#form_delete');
        $I->seeCurrentUrlEquals('/service');
        $I->see('a été supprimé avec succès', '.alert-success');
        //$I->dontSee('Codeception', 'td[headers="service-label"]' );
    }
}

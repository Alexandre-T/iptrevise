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
 * Machine Crud Cest.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class MachineCrudCest
{
    /**
     * I want to show all machines and manage them.
     *
     * @param FunctionalTester $I
     */
    public function crudMachine(FunctionalTester $I)
    {
        $I->wantToTest('I cannot access machine managment without login');
        $I->amOnPage('/machine');
        $I->seeCurrentUrlEquals('/login');

        $I->wantTo('be connected as an organiser');
        $I->connect('organiser@example.org','organiser');
        $I->seeCurrentUrlEquals('/machine');

        $I->wantToTest('Menu is well displayed');
        $I->see('Accueil');
        $I->dontSee('Accueil', '.active');
        $I->see('Déconnexion', '.navbar');
        $I->see('Machines', '.active');

        $I->wantToTest('the machine management page');
        $I->see('Machine 0', 'tr.row-1 td[headers="machine-label"]');
        $I->see('1', 'tr.row-1 td[headers="machine-interface"]');

        $I->wantTo('See the new machine form');
        $I->click('Nouvelle machine');
        $I->seeCurrentUrlEquals('/machine/new');
        $I->wantToTest('The required field');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/machine/new');

        //@TODO Je dois le voir plusieurs fois ou personnaliser chaque message d'erreur !
        $I->see('Cette valeur ne doit pas être vide.', '.help-block');

        $I->wantToTest('that I cannot create Doublon');
        $I->fillField('Intitulé', 'Machine 0');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/machine/new');
        $I->see('Cet intitulé de machine est déjà utilisé.', '.help-block');

        $I->wantToTest('The length of each field');
        $I->fillField('Intitulé', str_repeat('a', 33));
        $I->fillField('Interface', -2);
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/machine/new');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.', '.help-block');
        $I->see('Le nombre d’interface réseau doit être supérieur ou égal à "0".', '.help-block');

        $I->wantToTest('A valid form');
        $I->fillField('Intitulé', 'AMachine Codeception');
        $I->fillField('app_machine[description]', 'Description de codeception');
        $I->fillField('Interface', '9');
        $I->click('Créer');

        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/machine/$id");

        $I->wantToTest('the show Use Case');
        $I->see('La machine « AMachine Codeception » a été créée avec succès !', '.alert-success');

        $I->see('Informations générales');
        $I->see('AMachine Codeception', 'dd.lead');
        $I->see('Description de codeception', 'dd');
        $I->see('9', 'dd');

        $I->see('Journal de bord');
        $I->see('1', 'td[headers="logs-version"].row1');
        $I->see('Création', 'td[headers="logs-action"].row1');
        $I->see('organiser@example.org', 'td[headers="logs-user"].row1');
        $I->see('AMachine Codeception', 'td[headers="logs-value"].row1');
        $I->see('Description de codeception', 'td[headers="logs-value"].row1');
        $I->see('9', 'td[headers="logs-value"].row1');

        $I->see('Information');
        //@todo vérifier la date de création
        //@todo vérifier la date de modification

        $I->wantTo('Return to the list of machine and see my creation');
        $I->click(' Liste des machines');
        $I->seeCurrentUrlEquals('/machine');
        $I->see("AMachine Codeception", 'tr.row-1 td[headers="machine-label"]');
        $I->see("9", 'tr.row-1 td[headers="machine-interface"]');

        $I->wantTo('Edit my creation');
        $I->amOnPage("/machine/$id/edit");
        $I->seeCurrentUrlEquals("/machine/$id/edit");

        $I->wantTo('Test that the form is well initialized');
        $I->seeInField('Intitulé', 'AMachine Codeception');
        $I->seeInField('app_machine[description]', 'Description de codeception');
        $I->seeInField('Interface', '9');

        $I->fillField('Intitulé', 'AMachine Codeception');
        $I->fillField('app_machine[description]', 'Description de codeception2');
        $I->fillField('Interface', '11');

        $I->click('Éditer');

        $I->seeCurrentUrlEquals("/machine/$id");
        $I->see('La machine « AMachine Codeception » a été modifiée avec succès', '.alert-success');

        $I->see('Journal de bord');
        $I->see('2', 'td[headers="logs-version"].row2');
        $I->see('Modification', 'td[headers="logs-action"].row2');
        $I->see('organiser@example.org', 'td[headers="logs-user"].row2');
        $I->see('11', 'td[headers="logs-value"].row2');
        $I->see('Description de codeception2', 'td[headers="logs-value"].row2');
        $I->dontSee('AMachine Codeception', 'td[headers="logs-value"].row2');

        $I->wantTo('Delete my machine');
        $I->click('Supprimer', '#form_delete');
        $I->seeCurrentUrlEquals("/machine");
        $I->see('a été supprimée avec succès', '.alert-success');
        //$I->dontSee('Codeception', 'td[headers="machine-label"]' );

    }
}

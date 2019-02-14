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
 *
 */

/**
 * Network Crud Cest.
 *
 * @category Testing
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 */
class NetworkCrudCest
{
    /**
     * I want to show all networks and manage them.
     *
     * @param FunctionalTester $I
     */
    public function crudNetwork(FunctionalTester $I)
    {
        $I->wantToTest('I cannot access network managment without login');
        $I->amOnPage('/network');
        $I->seeCurrentUrlEquals('/login');

        $I->wantTo('be connected as an organiser');
        $I->connect('organiser@example.org','organiser');
        $I->seeCurrentUrlEquals('/network');

        $I->wantToTest('Menu is well displayed');
        $I->see('Accueil');
        $I->dontSee('Accueil', '.active');
        $I->see('Déconnexion', '.navbar');
        $I->see('Réseaux', '.active');

        $I->wantToTest('the network management page');
        $I->see('Network 0', 'td[headers=network-label]');
        $I->see('192.168.0.0/0', 'td[headers="network-address"]');

        $I->wantTo('See the new network form');
        $I->click('Nouveau réseau');
        $I->seeCurrentUrlEquals('/network/new');
        $I->wantToTest('The required field');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/network/new');

        //@TODO Je dois le voir plusieurs fois ou personnaliser chaque message d'erreur !
        $I->see('Cette valeur ne doit pas être vide.', '.help-block');

        $I->wantToTest('that I cannot create Doublon');
        $I->fillField('Réseau', 'Network 0');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/network/new');
        $I->see('Cet intitulé de réseau est déjà utilisé.', '.help-block');

        $I->wantToTest('The length of each field');
        $I->fillField('Réseau', str_repeat('a', 33));
        $I->fillField('Adresse réseau', 'foo');
        $I->fillField('Masque réseau (CIDR)', 33);
        $I->fillField('Couleur', 'bar');
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/network/new');
        $I->see('Cette chaîne est trop longue. Elle doit avoir au maximum 32 caractères.', '.help-block');
        //$I->see('Cette adresse IP n’est pas valide.', '.help-block');
        $I->see('Le masque (CIDR) doit être inférieur ou égal à 32.', '.help-block');
        $I->see('La couleur doit être au format hexadécimal.', '.help-block');

        $I->fillField('Masque réseau (CIDR)', -2);
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/network/new');
        $I->see('Le masque (CIDR) doit être supérieur ou égal à 0.', '.help-block');

        $I->fillField('Adresse réseau', '192.168.1.1');
        $I->fillField('Masque réseau (CIDR)', 24);
        $I->click('Créer');
        $I->seeCurrentUrlEquals('/network/new');
        $I->see('« 192.168.1.1/24 » n’est pas une adresse réseau valide. Est-ce que vous pensiez à « 192.168.1.0/24 » ?', '.alert-danger');

        $I->wantToTest('A valid form');
        $I->fillField('Réseau', 'ARéseau Codeception');
        $I->fillField('Couleur', '#00FF00');
        $I->fillField('app_network[description]', 'Description de codeception');
        $I->fillField('Adresse réseau', '194.194.0.0');
        $I->fillField('Masque réseau (CIDR)', '16');
        $I->click('Créer');

        $id = $I->grabFromCurrentUrl('~(\d+)~');
        $I->seeCurrentUrlEquals("/network/$id");

        $I->wantToTest('the show Use Case');
        $I->see('Le réseau « ARéseau Codeception » a été créé avec succès !', '.alert-success');

        $I->see('Informations générales');
        $I->see('ARéseau Codeception', 'dd.lead');
        $I->see('Description de codeception', 'dd');
        $I->see('194.194.0.0/16', 'dd');

        $I->see('Journal de bord');
        $I->see('1', 'td[headers="logs-version"].row1');
        $I->see('Création', 'td[headers="logs-action"].row1');
        $I->see('organiser@example.org', 'td[headers="logs-user"].row1');
        $I->see('ARéseau Codeception', 'td[headers="logs-value"].row1');
        $I->see('194.194.0.0', 'td[headers="logs-value"].row1');
        $I->see('16', 'td[headers="logs-value"].row1');

        $I->see('Information');
        //@todo vérifier la date de création
        //@todo vérifier la date de modification

        $I->wantTo('Return to the list of network and see my creation');
        $I->click(' Liste des réseaux');
        $I->seeCurrentUrlEquals('/network');
        $I->see("ARéseau Codeception", 'td[headers="network-label"]');
        $I->see("194.194.0.0/16", 'td[headers="network-address"]');

        $I->wantTo('Edit my creation');
        $I->amOnPage("/network/$id/edit");
        $I->seeCurrentUrlEquals("/network/$id/edit");

        $I->wantTo('Test that the form is well initialized');
        $I->seeInField('Réseau', 'ARéseau Codeception');
        $I->seeInField('Couleur', '#00FF00');
        $I->seeInField('app_network[description]', 'Description de codeception');
        $I->seeInField('Adresse réseau', '194.194.0.0');
        $I->seeInField('Masque réseau (CIDR)', '16');

        $I->fillField('Réseau', 'ARéseau Codeception');
        $I->fillField('Couleur', '#00F000');
        $I->fillField('app_network[description]', 'Description de codeception2');
        $I->fillField('Adresse réseau', '194.195.0.0');
        $I->fillField('Masque réseau (CIDR)', '18');

        $I->click('Éditer');

        $I->seeCurrentUrlEquals("/network/$id");
        $I->see('Le réseau « ARéseau Codeception » a été modifié avec succès', '.alert-success');

        $I->see('Journal de bord');
        $I->see('2', 'td[headers="logs-version"].row2');
        $I->see('Modification', 'td[headers="logs-action"].row2');
        $I->see('organiser@example.org', 'td[headers="logs-user"].row2');
        $I->see('00F000', 'td[headers="logs-value"].row2');
        $I->see('Description de codeception2', 'td[headers="logs-value"].row2');
        $I->see('194.195.0.0', 'td[headers="logs-value"].row2');
        $I->see('18', 'td[headers="logs-value"].row2');
        $I->dontSee('ARéseau Codeception', 'td[headers="logs-value"].row2');

        $I->wantTo('Delete my network');
        $I->click('Supprimer', '#form_delete');
        $I->seeCurrentUrlEquals("/network");
        $I->see('a été supprimé avec succès', '.alert-success');
        //$I->dontSee('Codeception', 'td[headers="network-label"]' );

    }
}

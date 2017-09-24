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
 *
 * @see       http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.txt
 */

namespace App\Controller;

use App\Bean\Factory\InformationFactory;
use App\Form\Type\NetworkType;
use App\Entity\Network;
use App\Manager\NetworkManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * NetworkController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("network")
 */
class NetworkController extends Controller
{
    /**
     * Limit of network per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all network entities.
     *
     * @Route("/", name="default_network_index")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_NETWORK')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $networkManager = $this->get(NetworkManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $networkManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'network.label', 'defaultSortDirection' => 'asc']
        );

        return $this->render('@App/default/network/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new network entity.
     *
     * @Route("/new", name="default_network_new")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_NETWORK')")
     *
     * @param Request $request
     *
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $network = new Network();
        $form = $this->createForm(NetworkType::class, $network);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $networkService = $this->get(NetworkManager::class);
            $networkService->save($network, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.network.created %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_network_show', array('id' => $network->getId()));
        }

        return $this->render('@App/default/network/new.html.twig', [
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a network entity.
     *
     * @Route("/{id}", name="default_network_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_NETWORK')")
     *
     * @param Network $network
     *
     * @return Response
     */
    public function showAction(Network $network)
    {
        /** @var NetworkManager $networkManager */
        $networkManager = $this->get(NetworkManager::class);
        $deleteForm = $this->createDeleteForm($network);
        $information = InformationFactory::createInformation($network);
        $logs = $networkManager->retrieveLogs($network);

        return $this->render('@App/default/network/show.html.twig', [
            'isDeletable' => $networkManager->isDeletable($network),
            'logs' => $logs,
            'information' => $information,
            'network' => $network,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing network entity.
     *
     * @Route("/{id}/edit", name="default_network_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_NETWORK')")
     *
     * @param Request $request The request
     * @param Network $network The network entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Network $network)
    {
        $networkService = $this->get(NetworkManager::class);
        $deleteForm = $this->createDeleteForm($network);
        $editForm = $this->createForm(NetworkType::class, $network);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $networkService->save($network, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.network.updated %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_network_show', array('id' => $network->getId()));
        }
        $logs = $networkService->retrieveLogs($network);
        $information = InformationFactory::createInformation($network);

        return $this->render('@App/default/network/edit.html.twig', [
            'isDeletable' => $networkService->isDeletable($network),
            'logs' => $logs,
            'information' => $information,
            'network' => $network,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a network entity.
     *
     * @Route("/{id}", name="default_network_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_MANAGE_NETWORK')")
     *
     * @param Request $request The request
     * @param Network $network The $network entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Network $network)
    {
        $form = $this->createDeleteForm($network);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $networkManager = $this->get(NetworkManager::class);
            $networkManager->delete($network);
            //Flash message.
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.network.deleted %name%', ['%name%' => $network->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        }

        return $this->redirectToRoute('default_network_index');
    }

    /**
     * Creates a form to delete a network entity.
     *
     * @param Network $network The network entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Network $network)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_delete', array('id' => $network->getId())))
            ->setMethod('DELETE')
            ->add('delete', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'trash-o',
                'label' => 'administration.delete.confirm.delete',
            ])
            ->getForm()
            ;
    }
}

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
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 */

namespace App\Controller;

use App\Bean\Factory\InformationFactory;
use App\Entity\Ip;
use App\Form\Type\IpType;
use App\Form\Type\NetworkType;
use App\Entity\Network;
use App\Manager\IpManager;
use App\Manager\NetworkManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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
 * @license Cerema 2017
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
     * @param Network    $network    The network entity
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
     * @param Network    $network    The $network entity
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
     * Reserve a new IP for the current network
     *
     * @Route("/{id}/new-ip", name="default_network_new_ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request
     * @param Network $network
     *
     * @return Response
     */
    public function newIp(Request $request, Network $network)
    {
        $ip = new Ip();
        $ip->setNetwork($network);
        $form = $this->createForm(IpType::class, $ip);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ipService = $this->get(IpManager::class);
            $ipService->save($ip, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.ip.created %name%', ['%name%' => long2ip($ip->getIp())]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_ip_show', array('id' => $ip->getId()));
        }

        return $this->render('@App/default/network/new-ip.html.twig', [
            'ip' => $ip,
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }
    /**
     * Reserve a new IP for the current network
     *
     * @Route("/unlink/{id}", name="default_network_new_machine")
     * @Security("is_granted('ROLE_MANAGE_IP','ROLE_MANAGE_MACHINE')")
     *
     * @param Network $network
     */
    public function newMachine(Network $network)
    {
        //FIXME Develop this use case
        die('@TODO');
    }
    /**
     * Unlink an IP from its machine
     *
     * @Route("/{id}/new-machine", name="default_network_unlink")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip $ip The IP entity
     *
     * @return RedirectResponse | Response
     *
     */
    public function unlinkAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        if (null == $ip->getMachine())
        {
            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('warning', $trans->trans('default.ip.unlink.error %ip%', [
                '%ip%' => ip2long($ip->getIp())
            ]));

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getNetwork()->getId()]);
        }

        $form = $this->createUnlinkForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before unlinking.
            $message = $trans->trans('default.ip.unlink %ip% %name%', [
                '%ip%' => ip2long($ip->getIp()),
                '%name%' => $ip->getMachine()->getLabel(),
            ]);

            //Unlinking
            $ipManager = $this->get(IpManager::class);
            $ipManager->unlink($ip);

            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('success', $message);

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getNetwork()->getId()]);
        } else {
            return $this->render('@App/default/network/unlink.html.twig',[
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
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
                'attr' => ['class' => 'fa-js-trash-o btn-danger confirm-delete'],
                'label' => 'administration.delete.confirm.delete',
            ])
            ->getForm()
            ;
    }

    /**
     * Creates a form to dissociate/unlink an ip from a machine.
     *
     * @param Ip $ip the Ip entity
     *
     * @return Form The form
     */
    private function createUnlinkForm(Ip $ip)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_unlink', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'fa-js-hand-spock-o btn-danger confirm-delete'],
                'label' => 'form.ip.unlink.confirm',
            ])
            ->getForm()
            ;
    }

    //@TODO Créer un use case permettant la translation d'IP (Passer de 192.168.0.0 à 192.168.1.0 par exemple
    //@TODO Créer un use case permettant de changer le masque

}

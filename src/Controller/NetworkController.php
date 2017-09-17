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
use App\Entity\Machine;
use App\Form\Type\IpMachineType;
use App\Form\Type\IpType;
use App\Form\Type\MachineType;
use App\Form\Type\NetworkType;
use App\Entity\Network;
use App\Manager\IpManager;
use App\Manager\MachineManager;
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
     * Deletes an ip from network.
     *
     * @Route("/{id}/delete-ip", name="default_network_delete_ip")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      the ip to delete
     *
     * @return RedirectResponse|Response
     */
    public function deleteIpAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        $form = $this->createDeleteIpForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before deleteing.
            $message = $trans->trans('default.ip.deleted %name%', [
                '%name%' => long2ip($ip->getIp()),
            ]);

            //Deleting
            $ipManager = $this->get(IpManager::class);
            $ipManager->delete($ip);

            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('success', $message);

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getNetwork()->getId()]);
        } else {
            return $this->render('@App/default/network/delete-ip.html.twig', [
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Link an IP to an existing machine.
     *
     * @Route("/{id}/link", name="default_network_link")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      The IP entity
     *
     * @return RedirectResponse | Response
     */
    public function linkAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        if (null !== $ip->getMachine()) {
            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('warning', $trans->trans('default.ip.link.error %ip% %name%', [
                '%ip%' => long2ip($ip->getIp()),
                '%name%' => $ip->getMachine()->getLabel(),
            ]));

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getNetwork()->getId()]);
        }

        $machineManager = $this->get(MachineManager::class);
        $machines = $machineManager->getAll();

        $form = $this->createLinkForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $machineId = (int) array_keys($form->getExtraData())[0];
            $machine = $machineManager->getMachineById($machineId);
            $session = $this->get('session');

            //Unfortunately form::isValid is returning FALSE and I do not know why
            if ($machine instanceof Machine) {
                //Link
                $ip->setMachine($machine);

                //Save
                $ipManager = $this->get(IpManager::class);
                $ipManager->save($ip);

                //Flash Message
                $session->getFlashBag()->add('success', $trans->trans('default.ip.link %ip% %name%', [
                    '%ip%' => long2ip($ip->getIp()),
                    '%name%' => $ip->getMachine()->getLabel(),
                ]));

                //Redirecting
                return $this->redirectToRoute('default_ip_show', ['id' => $ip->getId()]);
            } else {
                $session->getFlashBag()->add('warning', 'default.ip.no-more-machine');
            }
        }

        return $this->render('@App/default/network/link.html.twig', [
            'link_form' => $form->createView(),
            'machines' => $machines,
            'ip' => $ip,
        ]);
    }

    /**
     * Reserve a new IP for the current network.
     *
     * @Route("/{id}/new-ip", name="default_network_new_ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request
     * @param Network $network
     *
     * @return Response
     */
    public function newIpAction(Request $request, Network $network)
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
     * Reserve a new IP associated to a new machine for the current network.
     *
     * @Route("/{id}/new-ip-machine", name="default_network_new_ip_machine")
     * @Security("is_granted('ROLE_MANAGE_IP','ROLE_MANAGE_MACHINE')")
     *
     * @param Request $request
     * @param Network $network
     *
     * @return Response
     */
    public function newIpMachineAction(Request $request, Network $network)
    {
        $ip = new Ip();
        $machine = new Machine();
        $ip->setMachine($machine);
        $ip->setNetwork($network);
        $form = $this->createForm(IpMachineType::class, $ip);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ipService = $this->get(IpManager::class);
            $machineService = $this->get(MachineManager::class);
            $machineService->save($machine, $this->getUser());
            $ipService->save($ip, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.ip-machine.created %name% %ip%', [
                '%name%' => long2ip($ip->getIp()),
                '%ip%' => long2ip($ip->getIp()),
            ]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_ip_show', array('id' => $ip->getId()));
        }

        return $this->render('@App/default/network/new-ip-machine.html.twig', [
            'ip' => $ip,
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Link an IP to a new machine.
     *
     * @Route("/{id}/new-machine", name="default_network_new_machine")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      The IP entity
     *
     * @return RedirectResponse | Response
     */
    public function newMachineAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        if (null !== $ip->getMachine()) {
            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('warning', $trans->trans('default.ip.link.error %ip%', [
                '%ip%' => ip2long($ip->getIp()),
                '%name%' => $ip->getMachine()->getLabel(),
            ]));

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getNetwork()->getId()]);
        }

        $machine = new Machine();
        $ip->setMachine($machine);
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $machineService = $this->get(MachineManager::class);
            $machineService->save($machine, $this->getUser());
            $ipService = $this->get(IpManager::class);
            $ipService->save($ip, $this->getUser());

            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.machine.created %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_machine_show', array('id' => $machine->getId()));
        }

        return $this->render('@App/default/network/new-machine.html.twig', [
            'ip' => $ip,
            'machine' => $machine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Unlink an IP from its machine.
     *
     * @Route("/{id}/unlink", name="default_network_unlink")
     * @ParamConverter("ip", class="App:Ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      The IP entity
     *
     * @return RedirectResponse | Response
     */
    public function unlinkAction(Request $request, Ip $ip)
    {
        $trans = $this->get('translator.default');

        if (null === $ip->getMachine()) {
            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('warning', $trans->trans('default.ip.unlink.error %ip%', [
                '%ip%' => ip2long($ip->getIp()),
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
            return $this->render('@App/default/network/unlink.html.twig', [
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
     * Creates a form to ask confirmation before deleting an ip.
     *
     * @param Ip $ip the Ip entity
     *
     * @return Form The form
     */
    private function createDeleteIpForm(Ip $ip): Form
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_delete_ip', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'trash-o',
                'label' => 'form.ip.delete-ip.confirm',
            ])
            ->getForm()
            ;
    }

    /**
     * Creates a form to ssociate/link an ip to an existing machine.
     *
     * @param Ip $ip the Ip entity
     *
     * @return Form The form
     */
    private function createLinkForm(Ip $ip): Form
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_link', array('id' => $ip->getId())))
            ->setMethod('POST')
            ->getForm()
        ;

        return $form;
    }

    /**
     * Creates a form to dissociate/unlink an ip from a machine.
     *
     * @param Ip $ip the Ip entity
     *
     * @return Form The form
     */
    private function createUnlinkForm(Ip $ip): Form
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

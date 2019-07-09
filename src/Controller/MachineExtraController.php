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

use App\Entity\Ip;
use App\Entity\Machine;
use App\Entity\Network;
use App\Form\Type\IpType;
use App\Manager\IpManager;
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
 * Machine Controller for the extra-actions.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("machine")
 */
class MachineExtraController extends Controller
{
    /**
     * Deletes an ip from network.
     *
     * @Route("/{id}/delete-ip", name="default_machine_delete_ip")
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
            return $this->redirectToRoute('default_machine_show', ['id' => $ip->getMachine()->getId()]);
        } else {
            return $this->render('default/machine-extra/delete-ip.html.twig', [
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Link a machine to a free IP.
     *
     * @Route("/{machine_id}/link/{network_id}", name="default_machine_link")
     * @ParamConverter("machine", class="App:Machine", options={"id" = "machine_id"})
     * @ParamConverter("network", class="App:Network", options={"id" = "network_id"})
     * @Security("is_granted('ROLE_MANAGE_IP', 'ROLE_MANAGE_MACHINE')")
     *
     * @param Request $request The request
     * @param Machine $machine The machine entity
     * @param Network $network The network entity
     *
     * @return RedirectResponse | Response
     */
    public function linkAction(Request $request, Machine $machine, Network $network)
    {
        $trans = $this->get('translator.default');

        $ipManager = $this->get(IpManager::class);
        $ips = $ipManager->getFree($network);

        $form = $this->createLinkForm($machine, $network);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $ipId = (int) array_keys($form->getExtraData())[0];
            $ip = $ipManager->getById($ipId);
            $session = $this->get('session');

            //Unfortunately form::isValid is returning FALSE and I do not know why
            if ($ip instanceof Ip) {
                //Link
                $ip->setMachine($machine);

                //Save
                $ipManager->save($ip);

                //Flash Message
                $session->getFlashBag()->add('success', $trans->trans('default.ip.link %ip% %name%', [
                    '%ip%' => long2ip($ip->getIp()),
                    '%name%' => $ip->getMachine()->getLabel(),
                ]));

                //Redirecting
                return $this->redirectToRoute('default_ip_show', ['id' => $ip->getId()]);
            } else {
                $session->getFlashBag()->add('warning', 'default.machine.no-free-ip');
            }
        }

        return $this->render('default/machine-extra/link.html.twig', [
            'link_form' => $form->createView(),
            'ips' => $ips,
            'machine' => $machine,
            'network' => $network,
        ]);
    }

    /**
     * Reserve a new IP for the current machine.
     *
     * @Route("/{machine_id}/new-ip/{network_id}", name="default_machine_new_ip")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     * @ParamConverter("network", class="App:Network", options={"id" = "network_id"})
     * @ParamConverter("machine", class="App:Machine", options={"id" = "machine_id"})
     *
     * @param Request $request
     * @param Network $network
     * @param Machine $machine
     *
     * @return Response
     */
    public function newIpAction(Request $request, Network $network, Machine $machine)
    {
        $ip = new Ip();
        $ip->setNetwork($network);
        $ip->setMachine($machine);
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

        return $this->render('default/machine-extra/new-ip.html.twig', [
            'machine' => $machine,
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Unlink an IP from its machine.
     *
     * @Route("/{id}/unlink", name="default_machine_unlink")
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
                '%ip%' => long2ip($ip->getIp()),
            ]));

            //Redirecting
            return $this->redirectToRoute('default_machine_show', ['id' => $ip->getNetwork()->getId()]);
        }

        $form = $this->createUnlinkForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before unlinking.
            $message = $trans->trans('default.ip.unlink %ip% %name%', [
                '%ip%' => long2ip($ip->getIp()),
                '%name%' => $ip->getMachine()->getLabel(),
            ]);
            $idMachine = $ip->getMachine()->getId();

            //Unlinking
            $ipManager = $this->get(IpManager::class);
            $ipManager->unlink($ip);

            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('success', $message);

            //Redirecting
            return $this->redirectToRoute('default_machine_show', ['id' => $idMachine]);
        } else {
            return $this->render('default/machine-extra/unlink.html.twig', [
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
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
            ->setAction($this->generateUrl('default_machine_delete_ip', array('id' => $ip->getId())))
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
     * Creates a form to associate/link a machine to a free IP.
     *
     * @param Machine $machine the Ip entity
     * @param Network $network the Ip entity
     *
     * @return Form The form
     */
    private function createLinkForm(Machine $machine, Network $network): Form
    {
        $form = $this->createFormBuilder()
            ->setAction($this->generateUrl('default_machine_link', [
                'machine_id' => $machine->getId(),
                'network_id' => $network->getId(),
            ]))
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
            ->setAction($this->generateUrl('default_machine_unlink', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'hand-spock-o ',
                'label' => 'form.ip.unlink.confirm',
            ])
            ->getForm()
            ;
    }
}

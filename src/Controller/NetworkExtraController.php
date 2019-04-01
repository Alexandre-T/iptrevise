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
use App\Entity\Plage;
use App\Form\Type\IpMachineType;
use App\Form\Type\IpType;
use App\Form\Type\MachineType;
use App\Form\Type\PlageType;
use App\Entity\Network;
use App\Manager\IpManager;
use App\Manager\MachineManager;
use App\Manager\PlageManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
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
class NetworkExtraController extends Controller
{
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
            return $this->render('@App/default/network-extra/delete-ip.html.twig', [
                'confirm_form' => $form->createView(),
                'ip' => $ip,
            ]);
        }
    }

    /**
     * Deletes a plage from network.
     *
     * @Route("/{id}/delete-plage", name="default_network_delete_plage")
     * @ParamConverter("plage", class="App:Plage")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Plage      $plage      the plage to delete
     *
     * @return RedirectResponse|Response
     */
    public function deletePlageAction(Request $request,Plage $plage)
    {
        $network = $plage->getNetwork();

        $trans = $this->get('translator.default');

        $form = $this->createDeletePlageForm($plage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before deleteing.
            $message = $trans->trans('default.plage.deleted %name%', [
              '%name%' => $plage->getLabel()
            ]);

            //Deleting
            $plageManager = $this->get(PlageManager::class);
            $plageManager->delete($plage);

            //Flash Message
            $session = $this->get('session');
            $session->getFlashBag()->add('success', $message);

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $network->getId()]);
        } else {
            return $this->render('@App/default/network-extra/delete-plage.html.twig', [
                'confirm_form' => $form->createView(),
                'plage' => $plage,
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

        return $this->render('@App/default/network-extra/link.html.twig', [
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
        //Ip initialization
        $ip = new Ip();
        $ip->setNetwork($network);

        //Form initialization
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

        // The form was not submitted, the IP was not calculated
        // We purpose the first non-reserved IP in the Network
        if (null === $ip->getIp()) {
            $ipManager = $this->get(IpManager::class);
            $firstIp = $ipManager->getFirstNonReferencedIp($network);
            if (null === $firstIp) {
                //Flash message
                $session = $this->get('session');
                $trans = $this->get('translator.default');
                $message = $trans->trans('default.network.no.space');
                $session->getFlashBag()->add('error', $message);

                return $this->redirectToRoute('default_network_show', array('id' => $network->getId()));
            } else {
                $ip->setIp($firstIp);
                // We must set it.
                $form->setData($ip);
            }
        }

        return $this->render('@App/default/network-extra/new-ip.html.twig', [
            'ip' => $ip,
            'network' => $network,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reserve a new plage for the current network.
     *
     * @Route("/{id}/new-plage", name="default_network_new_plage")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request
     * @param Network $network
     *
     * @return Response
     */
    public function newPlageAction(Request $request, Network $network)
    {
        //Ip initialization
        // $ip = new Ip();
        // $ip->setNetwork($network);

        //Plage initialization
        $plage = new Plage();
        $plage->setNetwork($network);

        //Form initialization
        $form = $this->createForm(PlageType::class, $plage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $plageService = $this->get(PlageManager::class);
            $plageService->save($plage, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.plage.created %ipdeb% %ipfin%', ['%ipdeb%' => long2ip($plage->getStart()), '%ipfin%' => long2ip($plage->getEnd())]);

            $session->getFlashBag()->add('success', $message);

            $plages = $network->getPlages();
            $check = 0;
            foreach ($plages as &$plagesNetwork){
              if ($plage->getId() != $plagesNetwork->getId()){
                if ($plage->getStart() <= $plagesNetwork->getEnd() && $plage->getStart() >= $plagesNetwork->getStart()){
                  $check++;
                }
                if ($plage->getEnd() <= $plagesNetwork->getEnd() && $plage->getEnd() >= $plagesNetwork->getStart()){
                  $check++;
                }
              }
            }
            if ($check > 0){
              $warning  = $trans->trans('form.plage.error.plage.mixed');
              $session->getFlashBag()->add('warning', $warning);
            }
            $check = 0;
            $ips = $plage->getNetwork()->getIps();
            foreach ($ips as $ip){
              if ($ip->getIp() >= $plage->getStart() && $ip->getIp() <= $plage->getEnd()){
                $check++;
              }
            }
            if ($check > 0){
              $warning = $trans->trans('form.plage.error.ip.unique');
              $session->getFlashBag()->add('warning', $warning);
            }

            return $this->redirectToRoute('default_plage_show', array('id' => $plage->getId()));
        }

        // The form was not submitted, the IP was not calculated
        // We purpose the first non-reserved IP in the Network
        // if (null === $plage->getStart()) {
        //     $ipManager = $this->get(PlageManager::class);
        //     $firstIp = $ipManager->getFirstNonReferencedIp($network);
        //     if (null === $firstIp) {
        //         //Flash message
        //         $session = $this->get('session');
        //         $trans = $this->get('translator.default');
        //         $message = $trans->trans('default.network.no.space');
        //         $session->getFlashBag()->add('error', $message);
        //
        //         return $this->redirectToRoute('default_network_show', array('id' => $network->getId()));
        //     } else {
        //         $plage->setStart($firstIp);
        //         // We must set it.
        //         $form->setData($plage);
        //     }
        // }

        return $this->render('@App/default/network-extra/new-plage.html.twig', [
            'plage' => $plage,
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

        return $this->render('@App/default/network-extra/new-ip-machine.html.twig', [
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
                '%ip%' => long2ip($ip->getIp()),
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

        return $this->render('@App/default/network-extra/new-machine.html.twig', [
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
                '%ip%' => long2ip($ip->getIp()),
            ]));

            //Redirecting
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getNetwork()->getId()]);
        }

        $form = $this->createUnlinkForm($ip);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Prepare the message before unlinking.
            $message = $trans->trans('default.ip.unlink %ip% %name%', [
                '%ip%' => long2ip($ip->getIp()),
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
            return $this->render('@App/default/network-extra/unlink.html.twig', [
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
     * @return FormInterface The form
     */
    private function createDeleteIpForm(Ip $ip): FormInterface
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
     * Creates a form to ask confirmation before deleting an plage.
     *
     * @param Plage $ip the Ip entity
     *
     * @return FormInterface The form
     */
    private function createDeletePlageForm(Plage $plage): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_delete_plage', array('id' => $plage->getLabel())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'trash-o',
                'label' => 'form.plage.delete-plage.confirm',
            ])
            ->getForm()
            ;
    }

    /**
     * Creates a form to ssociate/link an ip to an existing machine.
     *
     * @param Ip $ip the Ip entity
     *
     * @return FormInterface The form
     */
    private function createLinkForm(Ip $ip): FormInterface
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
     * @return FormInterface The form
     */
    private function createUnlinkForm(Ip $ip): FormInterface
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_network_unlink', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('confirm', SubmitType::class, [
                'attr' => ['class' => 'btn-danger confirm-delete'],
                'icon' => 'hand-spock-o',
                'label' => 'form.ip.unlink.confirm',
            ])
            ->getForm()
            ;
    }

    //@TODO Créer un use case permettant la translation d'IP (Passer de 192.168.0.0 à 192.168.1.0 par exemple
    //@TODO Créer un use case permettant de changer le masque
}

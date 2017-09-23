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
use App\Entity\Ip;
use App\Form\Type\IpType;
use App\Manager\IpManager;
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
 * IpController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("ip")
 */
class IpController extends Controller
{
    /**
     * Finds and displays a ip entity.
     *
     * @Route("/{id}", name="default_ip_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_IP')")
     *
     * @param Ip $ip
     *
     * @return Response
     */
    public function showAction(Ip $ip)
    {
        /** @var IpManager $ipManager */
        $ipManager = $this->get(IpManager::class);
        $deleteForm = $this->createDeleteForm($ip);
        $information = InformationFactory::createInformation($ip);
        $logs = $ipManager->retrieveLogs($ip);

        return $this->render('@App/default/ip/show.html.twig', [
            'isDeletable' => $ipManager->isDeletable($ip),
            'logs' => $logs,
            'information' => $information,
            'ip' => $ip,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing ip entity.
     *
     * @Route("/{id}/edit", name="default_ip_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      The ip entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Ip $ip)
    {
        $ipService = $this->get(IpManager::class);
        $deleteForm = $this->createDeleteForm($ip);
        $editForm = $this->createForm(IpType::class, $ip);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $ipService->save($ip, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.ip.updated %name%', ['%name%' => long2ip($ip->getIp())]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_ip_show', array('id' => $ip->getId()));
        }
        $logs = $ipService->retrieveLogs($ip);
        $information = InformationFactory::createInformation($ip);

        return $this->render('@App/default/ip/edit.html.twig', [
            'isDeletable' => $ipService->isDeletable($ip),
            'logs' => $logs,
            'information' => $information,
            'ip' => $ip,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * Deletes a ip entity.
     *
     * @Route("/{id}", name="default_ip_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Ip      $ip      The $ip entity
     *
     * @return RedirectResponse | Response
     */
    public function deleteAction(Request $request, Ip $ip)
    {
        $network = $ip->getNetwork();
        $machine = $ip->getMachine();

        $form = $this->createDeleteForm($ip);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ipManager = $this->get(IpManager::class);
            $ipManager->delete($ip);
            //Flash message.
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.ip.deleted %name%', ['%name%' => long2ip($ip->getIp())]);
            $session->getFlashBag()->add('success', $message);
        }

        if (null === $machine) {
            return $this->redirectToRoute('default_network_show', ['id' => $network->getId()]);
        } else {
            return $this->render('@App/default/ip/delete.html.twig', [
                'network' => $network,
                'machine' => $machine,
            ]);
        }
    }

    /**
     * Creates a form to delete a ip entity.
     *
     * @param Ip $ip The ip entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Ip $ip)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_ip_delete', array('id' => $ip->getId())))
            ->setMethod('DELETE')
            ->add('delete', SubmitType::class, [
                'attr' => ['class' => 'fa-js-trash-o btn-danger confirm-delete'],
                'label' => 'administration.delete.confirm.delete',
            ])
            ->getForm()
            ;
    }
}

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
use App\Security\Voter\IpVoter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
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
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("ip")
 */
class IpController extends Controller
{
    /**
     * Finds and displays a ip entity.
     *
     * @Route("/{id}", name="default_ip_show")
     * @Method("GET")
     *
     * @param Ip $ip
     *
     * @return Response
     */
    public function showAction(Ip $ip)
    {
        $this->denyAccessUnlessGranted(IpVoter::VIEW, $ip);

        /** @var IpManager $ipManager */
        $ipManager = $this->get(IpManager::class);
        $view = [];
        $isDeletable = $ipManager->isDeletable($ip);
        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($ip)->createView();
        }
        $information = InformationFactory::createInformation($ip);
        $logs = $ipManager->retrieveLogs($ip);

        return $this->render('default/ip/show.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'ip' => $ip,
        ]));
    }

    /**
     * Displays a form to edit an existing ip entity.
     *
     * @Route("/{id}/edit", name="default_ip_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request The request
     * @param Ip      $ip      The ip entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Ip $ip)
    {
        $this->denyAccessUnlessGranted(IpVoter::EDIT, $ip);
        $view = [];
        $ipService = $this->get(IpManager::class);
        $isDeletable = $ipService->isDeletable($ip);
        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($ip)->createView();
        }

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

        return $this->render('default/ip/edit.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'ip' => $ip,
            'edit_form' => $editForm->createView(),
        ]));
    }

    /**
     * Deletes a ip entity.
     *
     * @Route("/{id}", name="default_ip_delete")
     * @Method("DELETE")
     *
     * @param Request $request The request
     * @param Ip      $ip      The $ip entity
     *
     * @return RedirectResponse | Response
     */
    public function deleteAction(Request $request, Ip $ip)
    {
        $this->denyAccessUnlessGranted(IpVoter::EDIT, $ip);
        $network = $ip->getNetwork();
        $machine = $ip->getMachine();

        $form = $this->createDeleteForm($ip);
        $form->handleRequest($request);
        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $ipManager = $this->get(IpManager::class);
        $isDeletable = $ipManager->isDeletable($ip);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $ipManager->delete($ip);
            $message = $trans->trans('default.ip.deleted %name%', ['%name%' => long2ip($ip->getIp())]);
            $session->getFlashBag()->add('success', $message);
        }elseif (!$isDeletable){
            $message = $trans->trans('default.ip.not-deletable %name%', ['%name%' => long2ip($ip->getIp())]);
            $session->getFlashBag()->add('warning', $message);
            return $this->redirectToRoute('default_ip_show', ['id' => $ip->getId()]);
        }

        if (null === $machine) {
            return $this->redirectToRoute('default_network_show', ['id' => $network->getId()]);
        } else {
            return $this->render('default/ip/delete.html.twig', [
                'machine' => $machine,
                'network' => $network,
            ]);
        }
    }

    /**
     * Creates a form to delete a ip entity.
     *
     * @param Ip $ip The ip entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Ip $ip)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_ip_delete', array('id' => $ip->getId())))
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

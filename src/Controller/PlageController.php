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
use App\Entity\Network;
use App\Form\Type\NetworkType;
use App\Manager\NetworkManager;
use App\Entity\Plage;
use App\Form\Type\PlageType;
use App\Manager\PlageManager;
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
 * @Route("plage")
 */
class PlageController extends Controller
{

  /**
  * Limit of plage per page for listing
  */
  const LIMIT_PER_PAGE = 25;

  /**
   * Lists all plage entities.
   *
   * @Route("/", name="default_plage_index")
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

      return $this->render('@App/default/plage/index.html.twig', [
          'pagination' => $pagination,
      ]);
  }


    /**
     * Finds and displays a ip entity.
     *
     * @Route("/{id}", name="default_plage_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_IP')")
     *
     * @param Plage $plage
     *
     * @return Response
     */
    public function showAction(Plage $plage)
    {
        /** @var PlageManager $plageManager */
        $plageManager = $this->get(PlageManager::class);
        $view = [];
        $isDeletable = $this->isGranted('ROLE_MANAGE_IP') && $plageManager->isDeletable($plage);
        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($plage)->createView();
        }
        $information = InformationFactory::createInformation($plage);
        $logs = $plageManager->retrieveLogs($plage);

        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $network = $plage->getNetwork();
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

        return $this->render('@App/default/plage/show.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'plage' => $plage,
        ]));
    }

    /**
     * Displays a form to edit an existing plage entity.
     *
     * @Route("/{id}/edit", name="default_plage_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Plage      $plage      The plage entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Plage $plage)
    {
        $view = [];
        $plageService = $this->get(PlageManager::class);
        $isDeletable = $plageService->isDeletable($plage);
        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($plage)->createView();
        }

        $editForm = $this->createForm(PlageType::class, $plage);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $plageService->save($plage, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.plage.updated %name%', ['%name%' => $plage->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_plage_show', array('id' => $plage->getId()));
        }
        $logs = $plageService->retrieveLogs($plage);
        $information = InformationFactory::createInformation($plage);


        return $this->render('@App/default/plage/edit.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'plage' => $plage,
            'edit_form' => $editForm->createView(),
        ]));
    }

    /**
     * Deletes a plage entity.
     *
     * @Route("/{id}", name="default_plage_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_MANAGE_IP')")
     *
     * @param Request $request The request
     * @param Plage      $plage      The $plage entity
     *
     * @return RedirectResponse | Response
     */
    public function deleteAction(Request $request, Plage $plage)
    {
        $network = $plage->getNetwork();

        $form = $this->createDeleteForm($plage);
        $form->handleRequest($request);
        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $plageManager = $this->get(PlageManager::class);
        $isDeletable = $plageManager->isDeletable($plage);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $plageManager->delete($plage);
            $message = $trans->trans('default.plage.deleted %name%', ['%name%' => $plage->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        }elseif (!$isDeletable){
            $message = $trans->trans('default.plage.not-deletable %name%', ['%name%' => $plage->getLabel()]);
            $session->getFlashBag()->add('warning', $message);
            return $this->redirectToRoute('default_network_show', ['id' => $ip->getId()]);
        }

        return $this->render('@App/default/plage/delete.html.twig', [
            'network' => $network,
        ]);

    }

    /**
     * Creates a form to delete a ip entity.
     *
     * @param Plage $plage The ip entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Plage $plage)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_plage_delete', array('id' => $plage->getId())))
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

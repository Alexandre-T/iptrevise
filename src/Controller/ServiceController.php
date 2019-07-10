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
use App\Entity\Service;
use App\Form\Type\ServiceType;
use App\Manager\ServiceManager;
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
 * Service CRUD Controller.
 *
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("service") // faire une route "service"
 * @Security("is_granted('ROLE_ADMIN')")
 *
 */
class ServiceController extends Controller
{
    /**
     * Limit of services per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * List all service entities.
     *
     * @Route("/", name="default_service_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $serviceManager = $this->get(ServiceManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
          $serviceManager->getQueryBuilder(), /* queryBuilder NOT result */
          $request->query->getInt('page', 1)/*page number*/,
          self::LIMIT_PER_PAGE,
          ['defaultSortFieldName' => 'service.label', 'defaultSortDirection' => 'asc']
      );

        return $this->render('default/service/index.html.twig', [
          'pagination' => $pagination,
      ]);
    }

    /**
     * Creates a new service entity.
     *
     * @Route("/new", name="default_service_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service); // TODO ServiceType
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $serviceService = $this->get(ServiceManager::class); //TODO ServiceManager
            $serviceService->save($service, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.service.created %name%', ['%name%' => $service->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_service_show', array('id' => $service->getId()));
        }

        return $this->render('default/service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a service entity.
     *
     * @Route("/{id}", name="default_service_show")
     * @Method("GET")
     *
     * @param Service $service
     *
     * @return Response
     */
    public function showAction(Service $service)
    {
        /** @var ServiceManager $serviceManager */
        $serviceManager = $this->get(ServiceManager::class);

        $view = [];
        $view['information'] = InformationFactory::createInformation($service);
        $view['logs'] = $serviceManager->retrieveLogs($service);
        $view['service'] = $service;
        $view['isDeletable'] = $this->isGranted('ROLE_ADMIN') && $serviceManager->isDeletable($service);

        if ($view['isDeletable']) {
            $view['delete_form'] = $this->createDeleteForm($service)->createView();
        }

        return $this->render('default/service/show.html.twig', $view);
    }

    /**
     * Displays a form to edit an existing service entity.
     *
     * @Route("/{id}/edit", name="default_service_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request The request
     * @param Service $service The service entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Service $service)
    {
        $serviceService = $this->get(ServiceManager::class);
        $view = [];
        $isDeletable = $serviceService->isDeletable($service);

        if ($isDeletable) {
            $view['delete_form'] = $this->createDeleteForm($service)->createView();
        }

        $editForm = $this->createForm(ServiceType::class, $service);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $serviceService->save($service, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.service.updated %name%', ['%name%' => $service->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_service_show', array('id' => $service->getId()));
        }
        $logs = $serviceService->retrieveLogs($service);
        $information = InformationFactory::createInformation($service);

        return $this->render('default/service/edit.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'service' => $service,
            'edit_form' => $editForm->createView(),
        ]));
    }

    /**
     * Deletes a service entity.
     *
     * @Route("/{id}", name="default_service_delete")
     * @Method("DELETE")
     *
     * @param Request $request The request
     * @param Service $service The $service entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Service $service)
    {
        $form = $this->createDeleteForm($service);
        $form->handleRequest($request);
        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $serviceManager = $this->get(ServiceManager::class);
        $isDeletable = $serviceManager->isDeletable($service);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $serviceManager->delete($service);

            $message = $trans->trans('default.service.deleted %name%', ['%name%' => $service->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        } elseif (!$isDeletable) {
            $message = $trans->trans('default.service.not-deletable %name%', ['%name%' => $service->getLabel()]);
            $session->getFlashBag()->add('warning', $message);

            return $this->redirectToRoute('default_service_show', ['id' => $service->getId()]);
        }

        return $this->redirectToRoute('default_service_index');
    }

    /**
     * Creates a form to delete a service entity.
     *
     * @param Service $service The service entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Service $service)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_service_delete', array('id' => $service->getId())))
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

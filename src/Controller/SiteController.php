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
use App\Form\Type\SiteType;
use App\Entity\Site;
use App\Manager\SiteManager;
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
 * SiteController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("site")
 */
class SiteController extends Controller
{
    /**
     * Limit of site per page for listing
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all site entities.
     *
     * @Route("/", name="default_site_index")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_SITE')")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $siteManager = $this->get(SiteManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $siteManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'site.label', 'defaultSortDirection' => 'asc']
        );

        return $this->render('@App/default/site/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new site entity.
     *
     * @Route("/new", name="default_site_new")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_SITE')")
     *
     * @param Request $request
     *
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $site = new Site();
        $siteService = $this->get(SiteManager::class);
        $sites = $siteService->getAll();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $siteService = $this->get(SiteManager::class);
            $siteService->save($site, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.site.created %name%', ['%name%' => $site->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_site_show', array('id' => $site->getId()));
        }

        return $this->render('@App/default/site/new.html.twig', [
            'site' => $site,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a site entity.
     *
     * @Route("/{id}", name="default_site_show")
     * @Method("GET")
     * @Security("is_granted('ROLE_READ_SITE')")
     *
     * @param Site $site
     *
     * @return Response
     */
    public function showAction(Site $site)
    {
      $this->denyAccessUnlessGranted('view', $site);
        /** @var SiteManager $siteManager */
        $siteManager = $this->get(SiteManager::class);

        $view = [];
        $view['information'] = InformationFactory::createInformation($site);
        $view['logs'] = $siteManager->retrieveLogs($site);
        $view['site'] = $site;
        $view['isDeletable'] = $this->isGranted('ROLE_ADMIN') && $siteManager->isDeletable($site);

        if ($view['isDeletable']){
            $view['delete_form'] = $this->createDeleteForm($site)->createView();
        }

        return $this->render('@App/default/site/show.html.twig', $view);
    }

    /**
     * Displays a form to edit an existing site entity.
     *
     * @Route("/{id}/edit", name="default_site_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_SITE')")
     *
     * @param Request $request The request
     * @param Site $site The site entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Site $site)
    {
        $this->denyAccessUnlessGranted('edit', $site);

        $siteService = $this->get(SiteManager::class);
        $view = [];
        $isDeletable = $siteService->isDeletable($site);

        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($site)->createView();
        }

        $editForm = $this->createForm(SiteType::class, $site);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $siteService->save($site, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.site.updated %name%', ['%name%' => $site->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_site_show', array('id' => $site->getId()));
        }
        $logs = $siteService->retrieveLogs($site);
        $information = InformationFactory::createInformation($site);

        return $this->render('@App/default/site/edit.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'site' => $site,
            'edit_form' => $editForm->createView(),
        ]));
    }

    /**
     * Deletes a site entity.
     *
     * @Route("/{id}", name="default_site_delete")
     * @Method("DELETE")
     * @Security("is_granted('ROLE_ADMIN')")
     *
     * @param Request $request The request
     * @param Site $site The $site entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Site $site)
    {
        $this->denyAccessUnlessGranted('edit', $site);

        $form = $this->createDeleteForm($site);
        $form->handleRequest($request);
        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $siteManager = $this->get(SiteManager::class);
        $isDeletable = $siteManager->isDeletable($site);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $siteManager->delete($site);

            $message = $trans->trans('default.site.deleted %name%', ['%name%' => $site->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        }elseif (!$isDeletable){

            $message = $trans->trans('default.site.not-deletable %name%', ['%name%' => $site->getLabel()]);
            $session->getFlashBag()->add('warning', $message);
            return $this->redirectToRoute('default_site_show', ['id' => $site->getId()]);
        }

        return $this->redirectToRoute('default_site_index');
    }

    /**
     * Creates a form to delete a site entity.
     *
     * @param Site $site The site entity
     *
     * @return Form The form
     */
    private function createDeleteForm(Site $site)
    {
        
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_site_delete', array('id' => $site->getId())))
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

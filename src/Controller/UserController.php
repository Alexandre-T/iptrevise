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
 *
 */
namespace App\Controller;

use App\Bean\Factory\InformationFactory;
use App\Form\Type\UserType;
use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * UserController class.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 * @Route("administration/user")
 *
 */
class UserController extends Controller
{
    /**
     * Limit of user per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all user entities.
     *
     * @Route("/", name="administration_user_index")
     * @Method("GET")
     *
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        //Retrieving all services
        $userManager = $this->get(UserManager::class);
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $userManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'user.label', 'defaultSortDirection' => 'asc']
        );
        return $this->render('@App/administration/user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/new", name="administration_user_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $userService = $this->get(UserManager::class);
            $userService->save($user, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('administration.user.created _name_', ['name' => $user->getUsername()]);
            $session->getFlashBag()->add('success', $message);
            return $this->redirectToRoute('administration_user_show', array('id' => $user->getId()));
        }
        return $this->render('@App/administration/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * Finds and displays a user entity.
     *
     * @Route("/{id}", name="administration_user_show")
     * @Method("GET")
     *
     * @param User $user
     * @return Response
     */
    public function showAction(User $user)
    {
        /** @var USerManager $userManager */
        $userManager = $this->get(UserManager::class);
        $deleteForm = $this->createDeleteForm($user);
        $information = InformationFactory::createInformation($user);
        $logs = $userManager->retrieveLogs($user);
        return $this->render('@App/administration/user/show.html.twig', [
            'isDeletable' => $userManager->isDeletable($user),
            'logs' => $logs,
            'information' => $information,
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ]);
    }
    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("/{id}/edit", name="administration_user_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request The request
     * @param User $user The user entity
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, User $user)
    {
        $userService = $this->get(UserManager::class);
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm(UserType::class, $user);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $userService->save($user, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('administration.user.updated _name_', ['name' => $user->getUsername()]);
            $session->getFlashBag()->add('success', $message);
            return $this->redirectToRoute('administration_user_show', array('id' => $user->getId()));
        }
        $logs = $userService->retrieveLogs($user);
        $information = InformationFactory::createInformation($user);
        return $this->render('@App/administration/user/edit.html.twig', [
            'isDeletable' => $userService->isDeletable($user),
            'logs' => $logs,
            'information' => $information,
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ]);
    }
    /**
     * Deletes a user entity.
     *
     * @Route("/{id}", name="administration_user_delete")
     * @Method("DELETE")
     *
     * @param Request $request The request
     * @param User $user The $user entity
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);
        dump($form->isValid());
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            //Flash message.
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('administration.user.deleted _name_', ['name' => $user->getUsername()]);
            $session->getFlashBag()->add('success', $message);
        }
        return $this->redirectToRoute('administration_user_index');
    }
    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('administration_user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}

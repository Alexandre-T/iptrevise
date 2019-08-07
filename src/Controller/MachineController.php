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
use App\Form\Type\MachineType;
use App\Entity\Machine;
use App\Manager\MachineManager;
use App\Manager\NetworkManager;
use App\Security\Voter\MachineVoter;
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
 * Machine CRUD Controller.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("machine")
 */
class MachineController extends Controller
{
    /**
     * Limit of machine per page for listing.
     */
    const LIMIT_PER_PAGE = 25;

    /**
     * Lists all machine entities.
     *
     * @Route("/", name="default_machine_index")
     * @Method("GET")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $this->denyAccessUnlessGranted(MachineVoter::LIST, new Machine());
        //Retrieving all services
        $machineManager = $this->get(MachineManager::class);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $machineManager->getQueryBuilder(), /* queryBuilder NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            self::LIMIT_PER_PAGE,
            ['defaultSortFieldName' => 'machine.label', 'defaultSortDirection' => 'asc']
        );

        return $this->render('default/machine/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Creates a new machine entity.
     *
     * @Route("/new", name="default_machine_new")
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     *
     * @return RedirectResponse |Response
     */
    public function newAction(Request $request)
    {
        $machine = new Machine();
        $this->denyAccessUnlessGranted(MachineVoter::CREATE, $machine);
        $form = $this->createForm(MachineType::class, $machine);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $machineService = $this->get(MachineManager::class);
            $machineService->save($machine, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.machine.created %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_machine_show', array('id' => $machine->getId()));
        }

        return $this->render('default/machine/new.html.twig', [
            'machine' => $machine,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Finds and displays a machine entity.
     *
     * @Route("/{id}", name="default_machine_show")
     * @Method("GET")
     *
     * @param Machine $machine
     *
     * @return Response
     */
    public function showAction(Machine $machine)
    {
        /** @var MachineManager $machineManager */
        $this->denyAccessUnlessGranted(MachineVoter::VIEW, $machine);
        $machineManager = $this->get(MachineManager::class);
        $networkManager = $this->get(NetworkManager::class);
        $view['isDeletable'] = $machineManager->isDeletable($machine);

        if ($view['isDeletable']){
            $view['delete_form'] = $this->createDeleteForm($machine)->createView();
        }

        $information = InformationFactory::createInformation($machine);
        $logs = $machineManager->retrieveLogs($machine);
        $networks = $networkManager->getEditableBySite($this->getUser());

        return $this->render('default/machine/show.html.twig', array_merge($view, [
            'logs' => $logs,
            'information' => $information,
            'machine' => $machine,
            'networks' => $networks,
        ]));
    }

    /**
     * Displays a form to edit an existing machine entity.
     *
     * @Route("/{id}/edit", name="default_machine_edit")
     * @Method({"GET", "POST"})
     *
     * @param Request $request The request
     * @param Machine $machine The machine entity
     *
     * @return RedirectResponse|Response
     */
    public function editAction(Request $request, Machine $machine)
    {
        $this->denyAccessUnlessGranted(MachineVoter::EDIT, $machine);
        $view = [];
        $machineService = $this->get(MachineManager::class);
        $isDeletable = $machineService->isDeletable($machine);

        if ($isDeletable){
            $view['delete_form'] = $this->createDeleteForm($machine)->createView();
        }

        $editForm = $this->createForm(MachineType::class, $machine);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $machineService->save($machine, $this->getUser());
            //Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.machine.updated %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_machine_show', array('id' => $machine->getId()));
        }
        $logs = $machineService->retrieveLogs($machine);
        $information = InformationFactory::createInformation($machine);

        return $this->render('default/machine/edit.html.twig', array_merge($view, [
            'isDeletable' => $isDeletable,
            'logs' => $logs,
            'information' => $information,
            'machine' => $machine,
            'edit_form' => $editForm->createView(),
        ]));
    }

    /**
     * Deletes a machine entity.
     *
     * @Route("/{id}", name="default_machine_delete")
     * @Method("DELETE")
     *
     * @param Request $request The request
     * @param Machine $machine The $machine entity
     *
     * @return RedirectResponse
     */
    public function deleteAction(Request $request, Machine $machine)
    {
        $this->denyAccessUnlessGranted(MachineVoter::EDIT, $machine);
        $form = $this->createDeleteForm($machine);
        $form->handleRequest($request);
        $session = $this->get('session');
        $trans = $this->get('translator.default');
        $machineManager = $this->get(MachineManager::class);
        $isDeletable = $machineManager->isDeletable($machine);

        if ($isDeletable && $form->isSubmitted() && $form->isValid()) {
            $machineManager->delete($machine);

            $message = $trans->trans('default.machine.deleted %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('success', $message);
        }elseif (!$isDeletable){

            $message = $trans->trans('default.machine.not-deletable %name%', ['%name%' => $machine->getLabel()]);
            $session->getFlashBag()->add('warning', $message);
            return $this->redirectToRoute('default_machine_show', ['id' => $machine->getId()]);
        }

        return $this->redirectToRoute('default_machine_index');
    }

    /**
     * Creates a form to delete a machine entity.
     *
     * @param Machine $machine The machine entity
     *
     * @return FormInterface The form
     */
    private function createDeleteForm(Machine $machine)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('default_machine_delete', array('id' => $machine->getId())))
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

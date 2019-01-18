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
use App\Form\Type\ServiceType; //TODO ServiceType
use App\Entity\Service;
use App\Manager\ServiceManager; // à faire

//regarder quelle son les use pertinent
use App\Manager\NetworkManager;
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
 * Service CRUD Controller
 * .
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @Route("service") // faire une route "service"
 */
class ServiceController extends Controller
{
    /**
     * Creates a new service entity.
     *
     * @Route("/new", name="default_service_new")
     * @Method({"GET", "POST"})
     * @Security("is_granted('ROLE_MANAGE_MACHINE')")
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
            $serviceService->save($service);
            /*Flash message
            $session = $this->get('session');
            $trans = $this->get('translator.default');
            $message = $trans->trans('default.service.created %name%', ['%name%' => $service->getLabel()]);
            $session->getFlashBag()->add('success', $message);

            return $this->redirectToRoute('default_service_show', array('id' => $service->getId()));*/
        }

        return $this->render('@App/default/service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }
}

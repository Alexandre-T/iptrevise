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

use App\Form\Type\LoginType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security Controller.
 *
 * @category Controller
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 */
class SecurityController extends Controller
{
    /**
     * Login Action.
     *
     * @return Response
     *
     * @Route("/login", name="security_login", methods={"get","post"})
     */
    public function loginAction()
    {
        // @TODO Put it in param ? AuthenticationUtils $authUtils ???
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $email = $authenticationUtils->getLastUsername();

        $form = $this->createForm(LoginType::class, ['mail' => $email]);

        return $this->render('@App/security/login.html.twig', array(
            'form'          => $form->createView(),
            'error'         => $error,
        ));
    }
}

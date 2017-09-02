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
namespace App\Security;

use App\Form\Type\LoginType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * LoginFormAuthenticator class.
 *
 * @category Security
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 * @see https://knpuniversity.com/screencast/symfony-security/authenticator-get-user-check-credentials
 *
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * Form factory interface.
     * If you want to know which class is your form factory, just type:
     * $ php ./bin/console debug:container form.factory
     *
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Entity Manager Interface.
     *
     * If you want to know which class is your entity manager, just type:
     * $ php ./bin/console debug:container entity_manager and select yours.
     *
     * @var EntityManager
     */
    private $em;

    /**
     * Router Interface.
     * If you want to know which class is your Router Interface, just type:
     * php ./bin/console debug:container router
     *
     * @var RouterInterface
     */
    private $router;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EntityManager $em
     * @param RouterInterface $router
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * Check credentials.
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];
        //@FIXME when it will works
        if ($password == 'iliketurtles') {
            return true;
        }

        return false;
    }

    /**
     * Provide credentials.
     *
     * @param Request $request
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');
        if (!$isLoginSubmit) {
            // skip authentication
            return null;
        }
        // TODO Change it to LoginForm
        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);

        //Store the identifiant to push it in the login form when credential errors occured..
        $data = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['mail']
        );

        return $data;
    }

    protected function getDefaultSuccessRedirectUrl()
    {
        return $this->router->generate('homepage');
    }

    /**
     * Generate the Login URL in router.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->router->generate('security_login');
    }

    /**
     * Get User
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['mail'];

        //@FIXME : ARGGGG Je meurs !!! a provider is better
        return $this->em
            ->getRepository('App:User')
            ->findOneBy(['mail' => $username]);
    }
}

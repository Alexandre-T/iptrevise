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

namespace App\Security;

use App\Form\LoginForm;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;

/**
 * LoginFormAuthenticator class.
 *
 * @category Security
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license CeCILL-B V1
 *
 * @see https://knpuniversity.com/screencast/symfony-security/authenticator-get-user-check-credentials
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * Form factory interface.
     * If you want to know which class is your form factory, just type:
     * $ php ./bin/console debug:container form.factory.
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
     * User password encoder.
     *
     * @var UserPasswordEncoder
     */
    private $passwordEncoder;

    /**
     * Router Interface.
     * If you want to know which class is your password encoder, just type:
     * php ./bin/console debug:container password_encoder.
     *
     * @var RouterInterface
     */
    private $router;

    /*
     * Target path trait for symfony4
     */
    use TargetPathTrait;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param EntityManager        $em
     * @param RouterInterface      $router
     * @param UserPasswordEncoder  $passwordEncoder
     */
    public function __construct(FormFactoryInterface $formFactory, EntityManager $em, RouterInterface $router, UserPasswordEncoder $passwordEncoder)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Check credentials.
     *
     * @param mixed         $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials['password'];

        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    /**
     * Provide credentials.
     *
     * @param Request $request
     *
     * @return mixed|null
     */
    public function getCredentials(Request $request)
    {
        //We create the form and handle the request
        $form = $this->formFactory->create(LoginForm::class);
        //Magic !!!!
        $form->handleRequest($request);

        //Store the identifiant to push it in the login form when credential errors occured..
        $data = $form->getData();
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data['mail']
        );
        /* @TODO Analyse */
//        if ($token == 'ILuvAPIs') {
//            throw new CustomUserMessageAuthenticationException(
//                'ILuvAPIs is not a real API key: it\'s just a silly phrase'
//            );
//        }

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
     * Get User.
     *
     * @param mixed                 $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return null|object
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials['mail']);
    }

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @param Request        $request
     * @param TokenInterface $token
     * @param string         $providerKey The provider (i.e. firewall) key
     *
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        //TODO: Add log like this project
        //https://github.com/Alexandre-T/jeuderole/blob/master/src/Security/AdminAuthenticator.php#L201
        if ($targetPath = $this->getTargetPath($request->getSession(), 'main')) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->router->generate('homepage'));
    }

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     *
     * @param Request                 $request
     * @param AuthenticationException $exception
     *
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        //FIXME: This code is not a good solution. Change it with explanation above.
        // TODO: Alexandre : If user is connected, return a 403 Response
        // TODO: Alexandre : If user is not connected, redirect to login page.
        return null;
    }

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns false, the authenticator will be skipped.
     *
     * So it return true when we are on login page and posting some information.
     *
     * @see https://symfonycasts.com/screencast/symfony4-upgrade/sf34-deprecations#deprecation-guardauthenticator-supports
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request)
    {
        return '/login' == $request->getPathInfo() && $request->isMethod('POST');
    }
}

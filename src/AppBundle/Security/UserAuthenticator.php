<?php

namespace AppBundle\Security;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


class UserAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoder;

    public function __construct(RouterInterface $router, EntityManagerInterface $em, EncoderFactoryInterface $encoder )
    {

        $this->router = $router;
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() != '/login' || !$request->isMethod('POST'))
            return;

        if ($request->request->has('_username') && $request->request->has('_password')) {
            return array(
                'username' => $request->request->get('_username'),
                'password' => $request->request->get('_password'),
            );
        } else {
            return;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if($credentials['username']){
            $user = $this->em->getRepository(User::class)->findOneByUsername($credentials['username']);
            if($user)
                return $user;
        }
        return;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        $encoder = $this->encoder->getEncoder($user);
        return $encoder->isPasswordValid($user->getPassword(), $credentials['password'], '');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        $url = $this->router->generate('login');
        return new RedirectResponse($url);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $url = $this->router->generate('homepage');
        return new RedirectResponse($url);
    }

    public function supportsRememberMe()
    {
        return true;
    }

}
<?php

namespace AppBundle\Service;

use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RedirectionService
{
    private $token;
    private $session;
    private $twig;
    private $request;
    private $container;
    private $urlRedirect;

    public function __construct(TokenStorageInterface $token,\Twig_Environment $twig, RequestStack $request, ContainerInterface $container)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->session = new Session();
        $this->token = $token->getToken()->getUser();
        $this->container = $container;
    }

    public function redirectionLogin()
    {

        // On stock les infos au cas ou si on est pas connecté.
        // Permet de redirigé par la suite, Voir SecurityController, checkLoginAction
        $request = $this->request->getCurrentRequest();

        // todo la recherche ne reste pas stocker en session du coup on ne lui passe pas encore au this->urlRedirect
        $recherche = $request->request->get('recherche');

        $this->urlRedirect['path'] = $request->get('_route');
        $this->urlRedirect['id'] = $request->get('id');

        $this->session->set('paramsRedirect', $this->urlRedirect);

    }


}

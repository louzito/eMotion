<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use FOS\UserBundle\Controller\RegistrationController as BaseController;


class SecurityController extends BaseController
{




    /**
     * @param Request $request
     *
     * @return Response
     */
    public function loginAction(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }

        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }

        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return $this->renderLogin(array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        ));
    }

    /**
     * Renders the login template with the given parameters. Overwrite this function in
     * an extended controller to provide additional data for the login template.
     *
     * @param array $data
     *
     * @return Response
     */
    protected function renderLogin(array $data)
    {
        return $this->render('@FOSUser/Security/login.html.twig', $data);
    }

    public function checkAction(Request $request)
    {

        // On récupère l'url de la page d'avant et redirect dessus.
        $session = new Session();
        $params = $session->get('paramsRedirect');
        $url = $params['path'];

        $username = $request->request->get('_username');
        $password = $request->request->get('_password');
        $token = $request->request->get('_csrf_token');

        $encryptUser = $this->postPersist($username,$password);
        $loginManager = $this->get('fos_user.security.login_manager');
        if (!empty($username) && !empty($password) && !empty($token)){
            $loginManager->logInUser($token,$encryptUser);
        }

        if (!is_null($params['id'])){
            return $this->redirectToRoute($url, array('id' => $params['id']));
        }
        else{
            return $this->redirectToRoute($url);
        }
//        return new RedirectResponse($url);
    }


    /**
     * @param $username
     * @param $password
     * Permet de vérifier si un mot de passe est déjà crypté, Si non on le cryte
     */
    private function postPersist($username,$password) {
        $em = $this->get('doctrine.orm.entity_manager');
        $repository = $em->getRepository('AppBundle:User');

        $user = $repository->findOneBy(array('username'=>$username));
        $passwordEncod = password_hash($password, PASSWORD_BCRYPT);

        $passwordVerifCryt = password_verify($password, $passwordEncod);
        if($passwordVerifCryt){
            if (!empty($user)){
                //$user->setPlainPassword($passwordEncod);
                $user->setPassword($passwordEncod);
                $em->persist($user);
                $em->flush();
                return $user;
            }
        }

    }

    public function logoutAction()
    {
        throw new \RuntimeException('You must activate the logout in your security firewall configuration.');
    }

}

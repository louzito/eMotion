<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



/**
 * metier controller.
 *
 * @Route("/manager")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="home_admin")
     */
    public function indexAction(Request $request)
    {
        return $this->render('admin/index.html.twig');
    }


}

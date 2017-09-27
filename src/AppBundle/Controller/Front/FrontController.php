<?php

namespace AppBundle\Controller\Front;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ReservationType;
use AppBundle\Entity\Reservation;

class FrontController extends Controller
{
    /**
     * @Route("/", name="front_homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('front/homepage.html.twig');
    }

    /**
     * @Route("/reservation", name="front_reservation")
     * @Security("has_role('ROLE_USER')")
     */
     public function reservationAction(Request $request)
     {
        $em = $this->getDoctrine()->getManager();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->persist($toolOrSkill);
            $em->flush();

            return $this->redirectToRoute('front_homepage');
        }

        return $this->render('front/reservation.html.twig', [
            'form' => $form->createView(),
        ]);
     }

    /**
     * @Route("/nos-offres", name="front_offres")
     */
     public function nosOffresAction(Request $request)
     {
        $em = $this->getDoctrine()->getManager();
        $offres = $em->getRepository('AppBundle:OffreLocation')->findAll();

        return $this->render('front/nos-offres.html.twig', [
            'offres' => $offres,
        ]);
     }

}

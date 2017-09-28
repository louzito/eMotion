<?php

namespace AppBundle\Controller\Front;

use AppBundle\AppBundle;
use AppBundle\Entity\Vehicule;
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
     * @Route("/reservation/{id}", name="front_reservation")
     * @Security("has_role('ROLE_USER')")
     */
     public function reservationAction(Request $request, $id)
     {
        $em = $this->getDoctrine()->getManager();
        $vehicule = $em->getRepository('AppBundle:Vehicule')->findOneBy(array('id' => $id));
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

         if ($form->isSubmitted() && $form->isValid()) {
             $reservation->setVehicule($vehicule);
             $reservation->setUser($this->getUser());
            $em->persist($reservation);
            $em->flush();

            $this->addFlash('success', 'Félicitation votre réservation à bien été enregistré');

            return $this->redirectToRoute('front_offres');
        }

        return $this->render('front/reservation.html.twig', [
            'vehicule' => $vehicule,
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

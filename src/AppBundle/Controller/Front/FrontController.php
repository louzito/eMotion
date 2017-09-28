<?php

namespace AppBundle\Controller\Front;

use AppBundle\Form\RechercheType;
use AppBundle\Traits\filterRechercheTrait;
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
    use filterRechercheTrait;

    /**
     * @Route("/", name="front_homepage")
     */
    public function indexAction(Request $request)
    {
        $rechercheForm = $this->createForm(RechercheType::class, null, array(
            'action' => $this->generateUrl('front_offres'),
            'method' => 'POST',
        ));

        return $this->render('front/homepage.html.twig', array(
            'rechercheForm' => $rechercheForm->createView(),
        ));
    }

    /**
     * @Route("/nos-offres", name="front_offres")
     */
    public function nosOffresAction(Request $request)
    {
        $offres = null;

        if($request->isMethod('POST')) {
            $offres = $this->getFilter($request);
            $dateDebut = new \DateTime('NOW');
            $dateFin = new \DateTime('NOW');
        }

        return $this->render('front/nos-offres.html.twig', [
            'offres' => $offres,
        ]);
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

}

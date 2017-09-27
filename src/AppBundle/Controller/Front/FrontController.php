<?php

namespace AppBundle\Controller\Front;

use AppBundle\Form\RechercheType;
use AppBundle\Traits\filterRechercheTrait;
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
        $filter = null;
        $rechercheForm = $this->createForm(RechercheType::class);

        if($request->isMethod('POST')) {
            $results = $this->getFilter($request);
        }

        return $this->render('front/homepage.html.twig', array(
            'rechercheForm' => $rechercheForm->createView(),
            'results' => $results,
        ));
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

}

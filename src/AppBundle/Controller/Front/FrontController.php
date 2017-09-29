<?php

namespace AppBundle\Controller\Front;

use AppBundle\Form\RechercheType;
use AppBundle\Service\OffreService;
use AppBundle\Traits\filterRechercheTrait;
use AppBundle\AppBundle;
use AppBundle\Entity\Vehicule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ReservationType;
use AppBundle\Entity\Reservation;
use Symfony\Component\HttpFoundation\Session\Session;

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

        if ($request->isMethod('POST')) {
            $offres = $this->getFilter($request);
            $session = new Session();
            $dateDebut = new \DateTime('NOW');
            $dateFin = new \DateTime('NOW');
            $session->set('dateDebut', $dateDebut);
            $session->set('dateFin', $dateDebut);
        }

        return $this->render('front/nos-offres.html.twig', [
            'offres' => $offres,
        ]);
    }

    /**
     * @Route("/reservation/{id}", name="front_reservation")
     * @Security("has_role('ROLE_USER')")
     */
    public function reservationAction($id, OffreService $offreService)
    {

        $etat = $this->getParameter('nonpayee');
        $offreService->newReservation($id, $etat);

        return $this->redirectToRoute('front_reservation_list');

    }

    /**
     * @Route("/reservation", name="front_reservation_list")
     * @Security("has_role('ROLE_USER')")
     */
    public function listReservationAction(Request $request, OffreService $offreService)
    {

        if ($request->get('stripeToken') != null) {
            $offreService->getIfPaid();
        }

        $offreService->getLengthReservation();
        $reservations = $offreService->getReservation();

        return $this->render('front/reservation.html.twig', array(
            'reservations' => $reservations
        ));

    }

    /**
     * @Route("/reservation/payment/{id}", name="payment_reservation")
     * @Security("has_role('ROLE_USER')")
     */
    public function paymentAction($id, OffreService $offreService)
    {

        $etat = $this->getParameter('payee');
        $reservation = $offreService->paymentReservation($id, $etat);

        return $this->render('front/_payment.html.twig', array(
            'reservation' => $reservation
        ));

    }

    public function moduleRechercheAction(){
        $rechercheForm = $this->createForm(RechercheType::class, null, array(
        'action' => $this->generateUrl('front_offres'),
        'method' => 'POST',
        ));

        return $this->render('front/module-de-recherche.html.twig', array(
            'form' => $rechercheForm->createView(),
        ));
    }

}

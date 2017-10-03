<?php

namespace AppBundle\Controller\Front;

use AppBundle\Form\RechercheType;
use AppBundle\Service\CookiesService;
use AppBundle\Service\OffreService;
use AppBundle\Traits\filterRechercheTrait;
use AppBundle\AppBundle;
use AppBundle\Entity\Vehicule;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ReservationType;
use AppBundle\Entity\Reservation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;

class FrontController extends Controller
{
    use filterRechercheTrait;

    /**
     * @Route("/", name="front_homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('front/homepage.html.twig');
    }

    /**
     * @Route("/nos-offres", name="front_offres")
     */
    public function nosOffresAction(Request $request, CookiesService $cookiesService)
    {
        $offres = null;
        $params = $request->get('recherche');

        if ($request->isMethod('POST')) {
            $offres = $this->getFilter($request);
            $session = new Session();
            $dateDebut = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateDebut']);
            $dateFin = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateFin']);
            $session->set('dateDebut', $dateDebut);
            $session->set('dateFin', $dateFin);
        }

        $response = $cookiesService->setCookiesRecherche($params);

        return $this->render('front/nos-offres.html.twig', [
            'offres' => $offres,
        ], $response);
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

    public function moduleRechercheAction(Request $request, CookiesService $cookiesService){
        $em = $this->getDoctrine()->getManager();
        $minEtmaxPrix = $em->getRepository('AppBundle:OffreLocation')->findMinEtMaxPrix();
        $vehicules = $em->getRepository('AppBundle:Vehicule')->findAll();

        $rechercheForm = $this->createForm(RechercheType::class, null, array(
        'action' => $this->generateUrl('front_offres'),
        'method' => 'POST',
        ));

        $rechercheForm = $cookiesService->setDataForm($request, $rechercheForm);

        return $this->render('front/module-de-recherche.html.twig', array(
            'form' => $rechercheForm->createView(),
            'minP' => $minEtmaxPrix['minP'],
            'maxP' => $minEtmaxPrix['maxP'],
            'vehicules' => $vehicules,
        ));
    }

}

<?php

namespace AppBundle\Controller\Front;

use AppBundle\Entity\OffreLocation;
use AppBundle\Form\RechercheType;
use AppBundle\Service\CookiesService;
use AppBundle\Service\OffreService;
use AppBundle\Service\PdfService;
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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\VarDumper\Tests\Fixture\DumbFoo;
use AppBundle\Form\ReservationParVoitureType;

class FrontController extends Controller
{
    use filterRechercheTrait;

    private $urlRedirect;

    public function __construct(RequestStack $request)
    {
        // On stock les infos au cas ou si on est pas connecté.
        // Permet de redirigé par la suite, Voir SecurityController, checkLoginAction
        $request = $request->getCurrentRequest();


        // todo la recherche ne reste pas stocker en session du coup on ne lui passe pas encore au this->urlRedirect
        $recherche = $request->request->get('recherche');

        $this->urlRedirect['path'] = $request->get('_route');
        $this->urlRedirect['id'] = $request->get('id');

        $session = new Session();
        $session->set('paramsRedirect', $this->urlRedirect);
    }

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
    public function nosOffresAction(Request $request, CookiesService $cookiesService, OffreService $offreService)
    {
        $offres = null;
        $params = $request->get('recherche');

        if ($request->isMethod('POST')) {
            $offres = $this->getFilter($request);
            $offreService->envoieDateSession();
            $interval = $offreService->getInterval();
            foreach($offres as $offre)
            {
                $offre->kmInclus = $interval*$offre->getKmJournalier();
            }
        }
        

        $response = $cookiesService->setCookiesRecherche($params);

        return $this->render('front/nos-offres.html.twig', [
            'offres' => $offres,
            'params' => $params,
        ], $response);
    }

    /**
     * @Route("/reservation/offre/{id}", name="front_reservation")
     * @Security("has_role('ROLE_USER')")
     */
    public function reservationAction(Request $request,$id, OffreService $offreService, PdfService $pdfService)
    {
        if ($request->get('stripeToken') != null) {
            $reservation = $offreService->getResa();
            $etat = $this->getParameter('payee');
            $reservationPaid = $offreService->getIfPaid($etat);
            $pdfService->generate($reservationPaid->getId());
            $offreService->sendConfirm($reservationPaid);
            return $this->redirect($this->generateUrl('front_reservation_detail', array('id' => $reservationPaid->getId())));
        } else {
            $etat = $this->getParameter('nonpayee');
            $reservation = $offreService->newReservation($id, $etat);
        }
        return $this->render('front/reservation-detail.html.twig',[
            'reservation' => $reservation['reservation'],
            'days' => $reservation['days'],
            'offre' => $reservation['offre'],

        ]);

    }

    /**
     * @Route("/reservation/{id}", name="front_reservation_detail")
     * @Security("has_role('ROLE_USER')")
     */
    public function reservationDetailAction($id, OffreService $offreService)
    {

        $reservation = $offreService->infoReservation($id);

        return $this->render('front/reservation-detail.html.twig',[
            'reservation' => $reservation['reservation'],
            'days' => $reservation['days'],
            'offre' => $reservation['offre']
        ]);

    }

    /**
     * @Route("/reservation", name="front_reservation_list")
     * @Security("has_role('ROLE_USER')")
     */
    public function listReservationAction(Request $request, OffreService $offreService)
    {

        $reservations = $offreService->reservationToken();

        return $this->render('front/reservation.html.twig', array(
            'reservations' => $reservations
        ));

    }

    /**
     * @Route("/reservation/remove/{id}", name="remove_notif")
     * @Security("has_role('ROLE_USER')")
     */
    public function checkNotifReservationAction(Request $request, OffreService $offreService)
    {

        $offreService->getLengthReservation(false);

        return $this->redirectToRoute('front_reservation_list');

    }

    /**
     * @Route("/reservation/check/ajax", name="check_reservation_ajax")
     * @Security("has_role('ROLE_USER')")
     */
    public function reservationAjaxAction(Request $request, OffreService $offreService)
    {

        if ($request->isXmlHttpRequest()){

            $filter = [];
            $dateDebut = \DateTime::createFromFormat('d/m/Y', $request->get('dateDebut'));
            $dateDebut = $dateDebut->format(DATE_ATOM);
            $filter['dateDebut'] = $dateDebut;

            $dateFin = \DateTime::createFromFormat('d/m/Y', $request->get('dateFin'));
            $dateFin = $dateFin->format(DATE_ATOM);
            $filter['dateFin'] = $dateFin;


            $idVehicule = $request->get('vehicule');
            $vehicule = $this->getDoctrine()->getRepository('AppBundle:Vehicule')->findOneBy(array('id' => $idVehicule));

            $bool = $this->isReservationDisponible($filter, $vehicule);
            if ($bool){
                return new Response('ok',200);
            }else{
                return new Response('ko',200);
            }

        }

        return new Response('Une erreur est survenue',400);

    }

    /**
     * @Route("/reservation/use-points/ajax", name="use_points")
     * @Security("has_role('ROLE_USER')")
     */
    public function usePointsAction(Request $request, OffreService $offreService)
    {
        $reservation = $offreService->getResa();

        if($request->request->get('checkboxPoints') == 'true') {
            $newPrix = $reservation->getPrixTotal() - ($this->getParameter('pointFideliteEnEuros') * $this->getUser()->getPointsFidelites());
            $newPrix = 0;
            $reservation->setPrixTotal($newPrix);
        } else {
            $reservation->setPrixTotal($reservation->getPrixInitial());
        }
        $offreService->setResa($reservation);
        return $this->render('front/paiement-points-fidelites.html.twig', [
            'reservation' => $reservation,
            'offreId' => $request->request->get('offreId'),
        ]);

    }
  
    /**
     * @Route("/fiche-vehicule/{id}", name="fiche_vehicule")
     */
    public function ficheVehiculeAction(Request $request, OffreLocation $offre, OffreService $offreService)
    {
        $listeDate = [];
        $form = $this->createForm(ReservationParVoitureType::class);

        if ($offre) {
            $reservations = $this->getReservationsParVehicule($offre->getVehicule()->getId());
            foreach($reservations as $r)
            {
                $interval = $r->getDateDebut()->diff($r->getDateFin())->d + 1;
                $dateD = $r->getDateDebut();
                $listeDate[] = $r->getDateDebut()->format('d-m-Y');
                for($i = 1; $i < $interval; $i++)
                {
                    $dateD->modify("+1 day");
                    $listeDate[] = $dateD->format('d-m-Y');
                }
            }
        }

        if ($request->isMethod('POST')) {
            $offreService->envoieDateSession();

            return $this->redirectToRoute('front_reservation', array(
                'id' => $offre->getId()
            ));
        }

        return $this->render('front/fiche-vehicule.html.twig', array(
            'offre' => $offre,
            'listeDateResa' => json_encode($listeDate),
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/liste-vehicule", name="liste_vehicule")
     */
    public function listeVehiculesAction()
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:OffreLocation');
        $offres = $em->findAll();

        return $this->render('front/liste-vehicule.html.twig', array(
            'offres' => $offres,
        ));
    }

    public function moduleRechercheAction(Request $request, CookiesService $cookiesService, $params = null){
        $em = $this->getDoctrine()->getManager();
        $minEtmaxPrix = $em->getRepository('AppBundle:OffreLocation')->findMinEtMaxPrix();
        $vehicules = $em->getRepository('AppBundle:Vehicule')->findAll();

        $rechercheForm = $this->createForm(RechercheType::class, null, array(
        'action' => $this->generateUrl('front_offres'),
        'method' => 'POST',
        ));

        $rechercheForm = $cookiesService->setDataForm($request, $rechercheForm, $params);

        return $this->render('front/module-de-recherche.html.twig', array(
            'form' => $rechercheForm->createView(),
            'minP' => $minEtmaxPrix['minP'],
            'maxP' => $minEtmaxPrix['maxP'],
            'vehicules' => $vehicules,
        ));
    }

    public function modulePreferencesAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:OffreLocation');
        $offres = $em->findRecommandations($request->cookies);

        return $this->render('front/template-preferences.html.twig', array(
            'offres' => $offres,
        ));
    }

    public function moduleDerniersVehiculesAction(Request $request)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:OffreLocation');

        return $this->render('front/template-derniers-vehicules.html.twig', array(
            'offres' => $em->findBy(array(), array('id' => 'DESC'), 3),
        ));
    }
}

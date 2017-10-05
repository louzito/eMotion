<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\OffreLocation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\RechercheAdminType;
use AppBundle\Form\RechercheAdminVehiculeType;
use AppBundle\Traits\filterRechercheTrait;
use AppBundle\Service\OffreService;
use Symfony\Component\HttpFoundation\Session\Session;


/**
 * @Route("/manager")
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminController extends Controller
{
    use filterRechercheTrait;
    /**
     * @Route("/", name="home_admin")
     */
    public function indexAction(Request $request)
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/locations", name="admin_show_locations")
     */
    public function gestionLocationsAction(Request $request, OffreService $offreService)
    {
        $em = $this->getDoctrine()->getManager();
        $reservationRepo = $em->getRepository('AppBundle:Reservation');
        $reservations = $reservationRepo->findAll();
        $form =  $this->forward('AppBundle:Admin/Admin:getFormLocation')->getContent();

        if($request->isMethod('POST')) {
            if ($request->get('stripeToken') != null) {
                $etat = $this->getParameter('payee');
                $reservationPaid = $offreService->getIfPaid($etat);
                return $this->redirectToRoute('admin_show_locations');
            }
        }
        return $this->render('admin/gestion-locations.html.twig', array(
            'reservations' => $reservations,
            'form' => $form,
        ));
    }

    /**
     * @Route("/location/{id}", name="admin_show_location")
     */
    public function showLocationAction(Reservation $reservation)
    {
        return $this->render('admin/show-location.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/location/{id}/update", name="admin_update_location")
     */
    public function updateMembreAction(Request $request, Reservation $reservation)
    {
        $em = $this->getDoctrine()->getManager();
        $reservationRepo = $em->getRepository('AppBundle:Reservation');
        $reservations = $reservationRepo->findAll();

        $form = $this->createForm(ReservationType::class, $reservation);
        $deleteForm = $this->createDeleteForm($reservation);
        
        if($request->isMethod('POST')) {
            $form->handleRequest($request);
            $em->persist($reservation);
            $em->flush();
            return $this->redirectToRoute('admin_show_locations');
        }


        return $this->render('admin/gestion-locations.html.twig', array(
            'reservations' => $reservations,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * @Route("/locations/ajax", name="admin_locations_ajax")
     */
    public function getLocationsAjax(Request $request)
    {
        $offres = null;
        if ($request->isMethod('POST')) {
            $offres = $this->getFilterAdmin($request);
            $dateDebut = \DateTime::createFromFormat('d/m/Y', $request->request->get('recherche_admin')['dateDebut']);
            $dateFin = \DateTime::createFromFormat('d/m/Y', $request->request->get('recherche_admin')['dateFin']);
            $interval = $dateDebut->diff($dateFin)->d + 1;
            foreach($offres as $offre)
            {
                $offre->kmInclus = $interval*$offre->getKmJournalier();
                $offre->prixTotal = $interval*$offre->getPrixJournalier();
            }
        }
        
        return $this->render('admin/offres-location.html.twig', [
            'offres' => $offres,
        ]);
    }

    /**
     * @Route("/locations/ajax/paiement", name="admin_locations_ajax_paiement")
     */
    public function getLocationsAjaxPaiement(Request $request, OffreService $offreService)
    {
        $resaForm = ($request->request->get('recherche_admin')) ? $request->request->get('recherche_admin') : null;
        if(!is_null($resaForm) && $request->isMethod('POST'))
        {
            $em = $this->getDoctrine()->getManager();
            $vehicule = $em->getRepository('AppBundle:OffreLocation')->find($resaForm['idVehicule']);
            $user = $em->getRepository('AppBundle:User')->findOneBy(['id' => $resaForm['user']]);
            $dateDebut = \DateTime::createFromFormat('d/m/Y', $resaForm['dateDebut']);
            $dateFin = \DateTime::createFromFormat('d/m/Y', $resaForm['dateFin']);
            $session = new Session();
            $session->set('dateDebut', $dateDebut);
            $session->set('dateFin', $dateFin);
            $etat = $this->getParameter('nonpayee');
            $reservation = $offreService->newReservation($resaForm['idOffreLocation'], $etat, $user);
            $filter = [
                'dateDebut' => $dateDebut->format(DATE_ATOM),
                'dateFin' => $dateFin->format(DATE_ATOM),
            ];
            if($this->isReservationDisponible($filter, $vehicule))
            {
                return $this->render('admin/paiement-location.twig', [
                    'reservation' => $reservation['reservation'],
                ]);
            }
            else{
                $message = "L'offre n'est plus disponible";
                return $this->render('admin/paiement-location.twig', [
                    'message' => $message,
                ]);
            }
             
        }
        else{
            $message = "Une erreur c'est produite";
            return $this->render('admin/paiement-location.twig', [
                'message' => $message,
            ]);
        }

    }

    /**
     * @Route("locations/ajax/form", name="admin_locations_ajax_form")
     */
    public function getFormLocationAction(Request $request)
    {
        if($request->isMethod('POST') && $request->request->get('type') == 'type-ajout-par-vehicule')
        {
            $idVehicule = ($request->request->get('idVehicule')) ? $request->request->get('idVehicule') : 0;
            $reservations = [];
            $listeDate = [];
            $em = $this->getDoctrine()->getManager();
            $offres = $em->getRepository('AppBundle:OffreLocation')->findAll();
            $form = $this->createForm(RechercheAdminVehiculeType::class);
            if($idVehicule != 0)
            {
                $reservations = $this->getReservationsParVehicule($idVehicule);
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

            return $this->render('admin/form-location-vehicule.html.twig', [
                    'offres' => $offres,
                    'idVehicule' => $idVehicule,
                    'form' => $form->createView(),
                    'reservations' => $reservations,
                    'listeDateResa' => json_encode($listeDate),
                ]);
        }
        else{
            $form = $this->createForm(RechercheAdminType::class);
            return $this->render('admin/form-location.html.twig', [
                    'form' => $form->createView(),
                ]);
        }
    }

}

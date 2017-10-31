<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Controller\InitController;
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
use AppBundle\Form\ReservationType;
use AppBundle\Form\RetourVehiculeType;
use AppBundle\Traits\filterRechercheTrait;
use AppBundle\Service\OffreService;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\PdfService;

/**
 * @Route("/manager")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ReservationController extends InitController
{
    use filterRechercheTrait;
    /**
     * @Route("/locations", name="admin_show_locations")
     */
    public function gestionLocationsAction(Request $request, OffreService $offreService, PdfService $pdfService)
    {
        $em = $this->getDoctrine()->getManager();
        $reservationRepo = $em->getRepository('AppBundle:Reservation');
        $typeTrie = ($request->query->get('type-trie')) ? $request->query->get('type-trie') : null;
        if(!is_null($typeTrie) && $typeTrie != "toute"){
            $reservations = $reservationRepo->findBy(array('etat' => $this->getParameter($typeTrie)));
        }else{
            $reservations = $reservationRepo->findAll();
        }
        
        $form =  $this->forward('AppBundle:Admin/Reservation:getFormLocation')->getContent();

        if($request->isMethod('POST')) {
            if ($request->get('stripeToken') != null) {
                $etat = $this->getParameter('payee');
                $reservationPaid = $offreService->getIfPaid($etat);
                $pdfService->generate($reservationPaid->getId());
                $offreService->sendConfirm($reservationPaid);
                return $this->redirectToRoute('admin_show_locations');
            }
        }
        return $this->render('admin/reservation/gestion-locations.html.twig', array(
            'typeTrie' => $typeTrie,
            'reservations' => $reservations,
            'form' => $form,
        ));
    }

    /**
     * @Route("/location/{id}", name="admin_show_location")
     */
    public function showLocationAction(Reservation $reservation)
    {
        return $this->render('admin/reservation/show-location.html.twig', [
            'reservation' => $reservation,
        ]);
    }


    /**
     * @Route("/location/{id}/retour", name="admin_retour_location")
     */
    public function retourLocationAction(Request $request, OffreService $offreService, PdfService $pdfService, Reservation $reservation)
    {
        $form = $this->createForm(RetourVehiculeType::class);

        if($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $rInfos = $request->request->get('retour_vehicule');
            $dateRetour = $rInfos['dateRetour']['year'] . '/' . $rInfos['dateRetour']['month'] . '/' . $rInfos['dateRetour']['day'];
            $dateRetour = new \DateTime($dateRetour);
            $reservation->setDateDeRetour($dateRetour);
            $kmParcouru = $rInfos['kmCptVehicule'] - $reservation->getVehicule()->getNbKilometres();
            if($kmParcouru > $reservation->getKmInclus() || $reservation->getDateFin() < $dateRetour){
                $coutSupplementaire = 0;
                if($reservation->getDateFin()->diff($dateRetour)->days > 0 || $reservation->getDateFin()->diff($dateRetour)->s > 0)
                {
                    if($reservation->getDateFin()->diff($dateRetour)->days > 0)
                    {
                        $jourDeRetard = $reservation->getDateFin()->diff($dateRetour)->days+1;
                    }
                    else
                    {
                        $jourDeRetard = 1;
                    }
                    $coutSupplementaire += $jourDeRetard * $this->container->getParameter('prixJourRetard');
                }
                $kmSupplementaire = $kmParcouru - $reservation->getKmInclus();
                if($kmSupplementaire > 0)
                {
                    $coutSupplementaire += $kmSupplementaire * $this->container->getParameter('prixKmEnPlus');
                }
                $reservation->setPrixTotal($reservation->getPrixInitial() + $coutSupplementaire);
            }
            $reservation->getVehicule()->setNbKilometres($rInfos['kmCptVehicule']);
            $reservation->setKmParcouru($kmParcouru);
            if($reservation->getPrixInitial() != $reservation->getPrixTotal()){
                $pdfService->generate($reservation->getId(), true);
            }
            $offreService->sendConfirmRetour($reservation);
            $reservation->setEtat($this->getParameter('terminee'));
            $em->flush();
            return $this->redirectToRoute('admin_show_locations');
        }

        return $this->render('admin/reservation/retour-vehicule.html.twig', array(
            'form' => $form->createView(),
            'reservation' =>$reservation,
        ));
    }

    /**
     * @Route("/location/{id}/update", name="admin_update_location")
     */
    public function updateLocationAction(Request $request, OffreService $offreService, Reservation $reservation)
    {
        $deleteForm = $this->createDeleteForm($reservation);
        if($request->isMethod('POST')) {
            if ($request->get('stripeToken') != null) {
                $etat = $this->getParameter('payee');
                $reservationPaid = $offreService->getIfPaid($etat);
                return $this->redirectToRoute('admin_show_locations');
            }
        }

        return $this->render('admin/reservation/location-edit.html.twig', array(
            'delete_form' => $deleteForm->createView(),
            'reservation' => $reservation,
        ));
    }

    /**
     * @Route("locations/ajax/update-form/{id}", name="admin_update_location_ajax_form")
     */
    public function getUpdateFormLocationAction(Request $request, Reservation $reservation)
    {
        if($request->isMethod('POST') && $request->request->get('type') == 'type-update-par-vehicule')
        {
            $idOffre = ($request->request->get('idOffre')) ? $request->request->get('idOffre') : 0;
            $reservations = [];
            $listeDate = [];
            $em = $this->getDoctrine()->getManager();
            $offres = $em->getRepository('AppBundle:OffreLocation')->findAll();
            $form = $this->createForm(RechercheAdminVehiculeType::class);
            $form->get('user')->setData($reservation->getUser());
            $form->get('idReservation')->setData($reservation->getId());
            if($idOffre != 0)
            {
                $offre = $em->getRepository('AppBundle:OffreLocation')->find($idOffre);
                $idVehicule = $offre->getVehicule()->getId();
                $form->get('idVehicule')->setData($idVehicule);
                $form->get('idOffreLocation')->setData($idOffre);
                $reservations = $this->getReservationsParVehicule($idVehicule);
                foreach($reservations as $r)
                {
                    $interval = $r->getDateDebut()->diff($r->getDateFin())->days + 1;
                    $dateD = $r->getDateDebut();
                    $listeDate[] = $r->getDateDebut()->format('d-m-Y');
                    for($i = 1; $i < $interval; $i++)
                    {
                        $dateD->modify("+1 day");
                        $listeDate[] = $dateD->format('d-m-Y');
                    }
                }
            }
            return $this->render('admin/reservation/form-location-vehicule-update.html.twig', [
                    'offres' => $offres,
                    'idOffre' => $idOffre,
                    'reservationId' => $reservation->getId(),
                    'form' => $form->createView(),
                    'listeDateResa' => json_encode($listeDate),
                ]);
        }
        else{
            $form = $this->createForm(RechercheAdminType::class);
            $form->get('user')->setData($reservation->getUser());
            $form->get('idReservation')->setData($reservation->getId());
            return $this->render('admin/reservation/form-location-update.html.twig', [
                    'form' => $form->createView(),
                ]);
        }
    }

    /**
     * @Route("/locations/ajax", name="admin_locations_ajax")
     */
    public function getLocationsAjax(Request $request, OffreService $offreService)
    {
        $offres = null;
        if ($request->isMethod('POST')) {
            $offres = $this->getFilterAdmin($request);
            $offreService->envoieDateSession();
            $interval = $offreService->getInterval();
            foreach($offres as $offre)
            {
                $offre->kmInclus = $interval*$offre->getKmJournalier();
                $offre->prixTotal = $interval*$offre->getPrixJournalier();
            }
        }
        
        return $this->render('admin/reservation/offres-location.html.twig', [
            'offres' => $offres,
        ]);
    }

    /**
     * @Route("/locations/update/ajax", name="admin_locations_update_ajax")
     */
    public function getLocationsUpdateAjax(Request $request, OffreService $offreService)
    {
        $offres = null;
        if ($request->isMethod('POST')) {
            $offres = $this->getFilterAdmin($request);
            $reservationId = $request->request->get('recherche_admin')['idReservation'];
            $offreService->envoieDateSession();
            $interval = $offreService->getInterval();
            foreach($offres as $offre)
            {
                $offre->kmInclus = $interval*$offre->getKmJournalier();
                $offre->prixTotal = $interval*$offre->getPrixJournalier();
            }
        }
        
        return $this->render('admin/reservation/offres-location-update.html.twig', [
            'offres' => $offres,
            'reservationId' => $reservationId,
        ]);
    }


    /**
     * @Route("/locations/ajax/paiement", name="admin_locations_ajax_paiement")
     */
    public function getLocationsAjaxPaiement(Request $request, OffreService $offreService)
    {
        switch (isset($request)) {
            case $request->request->get('recherche_admin'):
                $resaForm = $request->request->get('recherche_admin');
                break;
            case $request->request->get('recherche_admin_vehicule'):
                $resaForm = $request->request->get('recherche_admin_vehicule');
                break;
            default :
                $resaForm = null;
                break;
        }
        if(!is_null($resaForm) && $request->isMethod('POST'))
        {
            $em = $this->getDoctrine()->getManager();
            $vehicule = $em->getRepository('AppBundle:Vehicule')->find($resaForm['idVehicule']);
            $user = $em->getRepository('AppBundle:User')->findOneBy(['id' => $resaForm['user']]);
            $tabDate = $offreService->envoieDateSession();
            $etat = $this->getParameter('nonpayee');
            $reservation = $offreService->newReservation($resaForm['idOffreLocation'], $etat, $user, $resaForm['idReservation']);
            $filter = [
                'dateDebut' => $tabDate['dateDebut']->format(DATE_ATOM),
                'dateFin' => $tabDate['dateFin']->format(DATE_ATOM),
            ];
            if($this->isReservationDisponible($filter, $vehicule))
            {
                return $this->render('admin/reservation/paiement-location.twig', [
                    'reservation' => $reservation['reservation'],
                ]);
            }
            else{
                $message = "Une réservation a déjà été faite entre ces deux dates";
                return $this->render('admin/reservation/paiement-location.twig', [
                    'message' => $message,
                ]);
            }
             
        }
        else{
            $message = "Une erreur c'est produite";
            return $this->render('admin/reservation/paiement-location.twig', [
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
            $idOffre = ($request->request->get('idOffre')) ? $request->request->get('idOffre') : 0;
            $reservations = [];
            $listeDate = [];
            $em = $this->getDoctrine()->getManager();
            $offres = $em->getRepository('AppBundle:OffreLocation')->findAll();
            $form = $this->createForm(RechercheAdminVehiculeType::class);
            if($idOffre != 0)
            {
                $offre = $em->getRepository('AppBundle:OffreLocation')->find($idOffre);
                $idVehicule = $offre->getVehicule()->getId();
                $form->get('idVehicule')->setData($idVehicule);
                $form->get('idOffreLocation')->setData($idOffre);
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

            return $this->render('admin/reservation/form-location-vehicule.html.twig', [
                    'offres' => $offres,
                    'idOffre' => $idOffre,
                    'form' => $form->createView(),
                    'listeDateResa' => json_encode($listeDate),
                ]);
        }
        else{
            $form = $this->createForm(RechercheAdminType::class);
            return $this->render('admin/reservation/form-location.html.twig', [
                    'form' => $form->createView(),
                ]);
        }
    }
    
    /**
     * Deletes a reservation entity.
     *
     * @Route("/location/{id}/delete", name="admin_location_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Reservation $reservation)
    {
        $form = $this->createDeleteForm($reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($reservation);
            $em->flush();
        }

        return $this->redirectToRoute('admin_show_locations');
    }

    /**
     * Creates a form to delete a reservation entity.
     *
     * @param Reservation $reservation
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Reservation $reservation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_location_delete', array('id' => $reservation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}

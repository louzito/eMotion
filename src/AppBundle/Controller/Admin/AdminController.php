<?php

namespace AppBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\User;
use AppBundle\Entity\Reservation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Form\RechercheAdminType;
use AppBundle\Traits\filterRechercheTrait;


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
    public function gestionLocationsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $reservationRepo = $em->getRepository('AppBundle:Reservation');
        $reservations = $reservationRepo->findAll();
        $form = $this->createForm(RechercheAdminType::class);

        if($request->isMethod('POST')) {
            $resaForm = ($request->request->get('recherche_admin')) ? $request->request->get('recherche_admin') : null;
            if(!is_null($resaForm))
            {
                $reservation = new Reservation();
                $offre = $em->getRepository('AppBundle:OffreLocation')->find($resaForm['idOffreLocation']);
                $user = $em->getRepository('AppBundle:User')->find($resaForm['user']);
                $dateDebut = \DateTime::createFromFormat('d/m/Y', $resaForm['dateDebut']);
                $dateFin = \DateTime::createFromFormat('d/m/Y', $resaForm['dateFin']);
                $interval = $dateDebut->diff($dateFin)->d + 1;

                $reservation
                    ->setUser($user)
                    ->setVehicule($offre->getVehicule())
                    ->setDateDebut($dateDebut)
                    ->setDateFin($dateFin)
                    ->setPrixTotal($offre->getPrixJournalier*$interval);
                    
                // $em->persist($reservation);
                // $em->flush();
                return $this->redirectToRoute('admin_show_locations');
            }
        }


        return $this->render('admin/gestion-locations.html.twig', array(
            'reservations' => $reservations,
            'form' => $form->createView(),
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

}

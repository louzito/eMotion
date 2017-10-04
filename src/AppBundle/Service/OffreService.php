<?php

namespace AppBundle\Service;

use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class OffreService
{
    private $em;
    private $repository;
    private $repositoryReservation;
    private $token;
    private $session;
    private $twig;
    private $request;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token,\Twig_Environment $twig, RequestStack $request)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->request = $request;
        $this->session = new Session();
        $this->token = $token->getToken()->getUser();
        $this->repository = $em->getRepository('AppBundle:OffreLocation');
        $this->repositoryReservation = $em->getRepository('AppBundle:Reservation');
    }


    public function dateDiff($date1, $date2){
        $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
        $retour = array();

        $tmp = $diff;
        $retour['second'] = $tmp % 60;

        $tmp = floor( ($tmp - $retour['second']) /60 );
        $retour['minute'] = $tmp % 60;

        $tmp = floor( ($tmp - $retour['minute'])/60 );
        $retour['hour'] = $tmp % 24;

        $tmp = floor( ($tmp - $retour['hour'])  /24 );
        $retour['day'] = $tmp;

        return $retour;
    }

    public function envoieDateSession()
    {
        $request = $this->request->getCurrentRequest();

        $dateDebut = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateDebut']);
        $dateFin = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateFin']);

        $this->session->set('dateDebut', $dateDebut);
        $this->session->set('dateFin', $dateFin);
    }

    public function newReservation($offreSelected,$etat)
    {
        $offre = $this->repository->findOneBy(array('id' => $offreSelected));

        $dateDebut = $this->session->get('dateDebut');
        $dateFin = $this->session->get('dateFin');
        $interval = $dateDebut->diff($dateFin);
        $prixTotal = $interval->days * $offre->getPrixJournalier();

        $now = new \DateTime('now');
        $reservation = new Reservation();
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setVehicule($offre->getVehicule());
        $reservation->setPrixTotal($prixTotal);
        $reservation->setEtat($etat);
        $reservation->setDateReservation($now);
        $reservation->setUser($this->token);

        $this->session->set('reservationEnCours',$reservation);

        //$this->flush($reservation);
        $this->session->getFlashBag()->add('success', 'Félicitation votre réservation à bien été enregistré');

        return array(
            'reservation' => $reservation,
            'days' => $interval->days,
            'offre' => $offre
        );

    }

    public function getIfPaid($etat){

        $reservation = $this->session->get('reservationEnCours');

        $now = new \DateTime('now');

        $reservation->setEtat($etat);

        $reservation->setDatePaiement($now);

        $this->flush($reservation);

        $this->getLengthReservation();

        return $reservation;

    }

    public function infoReservation($id){

        $reservation = $this->repositoryReservation->findOneBy(array('id' => $id));

        $offre = $this->repository->findOneBy(array('id' => $reservation->getVehicule()->getId()));

        $dateDebut = $reservation->getDateDebut();
        $dateFin = $reservation->getDateFin();

        $interval = $dateDebut->diff($dateFin);

        return array(
            'reservation' => $reservation,
            'days' => $interval->days,
            'offre' => $offre
        );
    }


    public function reservationToken()
    {
        $reservation = $this->repositoryReservation->findByUser(array('user_id' => $this->token->getId()));
        return $reservation;
    }

    public function getReservationById($id){

        $reservation = $this->repositoryReservation->findOneBy(array('id' => $id));
        return $reservation;
    }
    public function flush($object)
    {
        $this->em->persist($object);

        $this->em->flush();
    }

    public function getLengthReservation($operator = true)
    {

        $reservations = $this->reservationToken();

        if ($operator){
            $length = 0;
            foreach ($reservations as $reservation){
                    $length = $length + 1;
            }

        }else{
            $length = $this->session->get('reservation');
            $length = $length - 1;
        }

        $this->session->set('reservation',$length);
    }

    public function paymentReservation($id,$etat)
    {
        $reservation = $this->repositoryReservation->findOneBy(array('id' => $id));

        $reservation->setEtat($etat);
        $this->session->set('reservationPaye', $reservation);

        return $reservation;

    }

    public function getvehiculebyid($idvehicule){


    }


}

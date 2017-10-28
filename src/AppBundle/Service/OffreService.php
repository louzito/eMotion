<?php

namespace AppBundle\Service;

use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class OffreService
{
    private $em;
    private $repository;
    private $repositoryReservation;
    private $token;
    private $session;
    private $twig;
    private $request;
    private $container;
    private $mailer;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token,\Twig_Environment $twig, RequestStack $request,ContainerInterface $container, MailerService $mailer)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->request = $request;
        $this->session = new Session();
        $this->token = (!is_null($token->getToken())) ? $token->getToken()->getUser() : null;
        $this->repository = $em->getRepository('AppBundle:OffreLocation');
        $this->repositoryReservation = $em->getRepository('AppBundle:Reservation');
        $this->container = $container;
        $this->mailer = $mailer;
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
        switch (isset($request)) {
            case $request->get('recherche'):
                $request = $request->get('recherche');
                break;
            case $request->get('recherche_admin'):
                $request = $request->get('recherche_admin');
                break;
            case $request->get('recherche_admin_vehicule'):
                $request = $request->get('recherche_admin_vehicule');
                break;
            case $request->get('reservation_par_voiture'):
                $request = $request->get('reservation_par_voiture');
                break;
        }
        $dateDebut = \DateTime::createFromFormat('d/m/Y', $request['dateDebut']);
        $dateFin = \DateTime::createFromFormat('d/m/Y', $request['dateFin']);
        $dateDebut = new \DateTime($dateDebut->format("Y-m-d")." 00:00:00");
        $dateFin = new \DateTime($dateFin->format("Y-m-d")." 23:59:59");

        $this->session->set('dateDebut', $dateDebut);
        $this->session->set('dateFin', $dateFin);

        return [
            'dateDebut' => $dateDebut,
            'dateFin' => $dateFin,
            ];
    }

    public function getInterval()
    {
        return $this->session->get('dateDebut')->diff($this->session->get('dateFin'))->days+1;
    }

    public function newReservation($offreSelected,$etat, $user =null, $reservationId = 0)
    {
        $offre = $this->repository->findOneBy(array('id' => $offreSelected));
        $dateDebut = $this->session->get('dateDebut');
        $dateFin = $this->session->get('dateFin');
        $interval = $this->getInterval();
        $prixInitial = $interval * $offre->getPrixJournalier();
        $kmInclus = $interval * $offre->getKmJournalier();

        $now = new \DateTime('now');
        if($reservationId != 0){
            $reservation = $this->getReservationById($reservationId);
        }
        else{
            $reservation = new Reservation();
        }
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setVehicule($offre->getVehicule());
        $reservation->setPrixInitial($prixInitial);
        $reservation->setPrixTotal($prixInitial);
        $reservation->setDateDeRetour($dateFin);
        $reservation->setKmInclus($kmInclus);
        $reservation->setEtat($etat);
        $reservation->setDateReservation($now);
        if(is_null($user))
        {
            $reservation->setUser($this->token);
        }
        else{
            $reservation->setUser($user);
        }
        

        $this->session->set('reservationEnCours',$reservation);

        $this->session->getFlashBag()->add('success', 'Félicitation votre réservation à bien été enregistré');

        return array(
            'reservation' => $reservation,
            'days' => $interval,
            'offre' => $offre
        );

    }

    public function getResa()
    {
        return $this->session->get('reservationEnCours');
    }

    public function getIfPaid($etat){

        $reservation = $this->session->get('reservationEnCours');

        $now = new \DateTime('now');

        $reservation->setEtat($etat);
        $reservation->setDatePaiement($now);
        // pb venant des sessions 
        $reservation->setVehicule($this->em->getRepository('AppBundle:Vehicule')->find($reservation->getVehicule()->getId()));
        $reservation->setUser($this->em->getRepository('AppBundle:User')->find($reservation->getUser()->getId()));
        
        //Cas d'un update on persist pas la réservation on flush directe
        if(!is_null($reservation->getId())){
            $reservation = $this->em->merge($reservation); // il faut merge car l'entité vient de la session et l'em la gere pas
            $this->em->flush();
        }else{
            $this->flush($reservation);
        }
        $this->getLengthReservation();

        return $reservation;

    }

    public function sendConfirm($reservation){
        $this->mailer->sendConfirmationLocation($reservation);
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

    public function setEtatEnCoursReservation()
    {
        $reservations = $this->repositoryReservation->findReservationEnCours();
        foreach($reservations as $resa)
        {
            $resa->setEtat($this->container->getParameter('enCours'));
        }
        $this->em->flush();
    }

    public function setEtatEnRetardReservation()
    {
        $reservations = $this->repositoryReservation->findReservationEnRetard();
        foreach($reservations as $resa)
        {
            $resa->setEtat($this->container->getParameter('enretard'));
            $this->mailer->sendEmailDeRetard($resa);
        }
        $this->em->flush();
    }
}

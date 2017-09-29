<?php

namespace AppBundle\Service;

use AppBundle\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token,\Twig_Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->session = new Session();
        $this->token = $token->getToken()->getUser();
        $this->repository = $em->getRepository('AppBundle:OffreLocation');
        $this->repositoryReservation = $em->getRepository('AppBundle:Reservation');
    }


    public function newReservation($offreSelected,$etat)
    {
        $offre = $this->repository->findOneBy(array('id' => $offreSelected));

        $dateDebut = $this->session->get('dateDebut');
        $dateFin = $this->session->get('dateFin');
        $prixTotal = 10;

        $reservation = new Reservation();
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setVehicule($offre->getVehicule());
        $reservation->setPrixTotal($prixTotal);
        $reservation->setEtat($etat);
        $reservation->setUser($this->token);

        $this->flush($reservation);
        $this->session->getFlashBag()->add('success', 'Félicitation votre réservation à bien été enregistré');

        return $reservation;

    }

    public function getReservation()
    {
        $reservation = $this->repositoryReservation->findByUser(array('user_id' => $this->token->getId()));
        return $reservation;
    }

    public function flush($object)
    {
        $this->em->persist($object);

        $this->em->flush();
    }

    public function getLengthReservation()
    {

        $reservations = $this->getReservation();
        $length = 0;

        foreach ($reservations as $reservation){
            $length = $length + 1;
            if ($reservation->getEtat() == '1'){
                $length = $length - 1;
            }
        }

        $this->session->set('reservation',$length);
    }

    public function getIfPaid(){
        $reservation = $this->session->get('reservationPaye');
        $id = $reservation->getId();
        $etat = $reservation->getEtat();
        $reservation = $this->repositoryReservation->findOneBy(array('id' => $id));
        $reservation->setEtat($etat);
        $this->flush($reservation);
    }

    public function paymentReservation($id,$etat)
    {
        $reservation = $this->repositoryReservation->findOneBy(array('id' => $id));

        $reservation->setEtat($etat);
        $this->session->set('reservationPaye', $reservation);

        return $reservation;

    }

}

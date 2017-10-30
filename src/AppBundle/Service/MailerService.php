<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerService
{
    private $mailer;
    private $container;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, ContainerInterface $container)
    {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->container = $container;
    }

    public function sendConfirmationLocation($reservation)
    {
        $pathPDF = __DIR__ . "/../../../web/uploads/facture/" . $reservation->getUser()->getNom() . "_" . $reservation->getUser()->getPrenom() . "_facture". $reservation->getId() .".pdf";
        $message = (new \Swift_Message('EMOTION : Votre véhicule à bien été réservé'))
            ->setFrom('emotion.paris.lyon@gmail.com')
            ->setTo($reservation->getUser()->getEmail())
            ->setContentType("text/html")
            ->attach(\Swift_Attachment::fromPath($pathPDF))
            ->setBody($this->container->get('templating')->render('email/confirmation-reservation.html.twig', array('reservation' => $reservation)))
        ;
        
        $this->mailer->send($message);
    }

    public function sendConfirmationRetour($reservation)
    {
        if($reservation->getPrixInitial() != $reservation->getPrixTotal()){
            $pathPDF = __DIR__ . "/../../../web/uploads/facture/" . $reservation->getUser()->getNom() . "_" . $reservation->getUser()->getPrenom() . "_facture". $reservation->getId() ."-2.pdf";
        } else {
            $pathPDF = __DIR__ . "/../../../web/uploads/facture/" . $reservation->getUser()->getNom() . "_" . $reservation->getUser()->getPrenom() . "_facture". $reservation->getId() .".pdf";
        }
        
        $message = (new \Swift_Message('EMOTION : Retour de votre véhicule'))
            ->setFrom('emotion.paris.lyon@gmail.com')
            ->setTo('zito.lou@gmail.com')
            ->setContentType("text/html")
            ->attach(\Swift_Attachment::fromPath($pathPDF))
            ->setBody($this->container->get('templating')->render('email/retour-vehicule.html.twig', array('reservation' => $reservation)))
        ;
        
        $this->mailer->send($message);
    }



    public function sendEmailDeRetard($reservation)
    {
        $message = (new \Swift_Message('EMOTION : Retard sur le retour de votre véhicule'))
            ->setFrom('emotion.paris.lyon@gmail.com')
            ->setTo($reservation->getUser()->getEmail())
            ->setContentType("text/html")
            ->setBody($this->container->get('templating')->render('email/retard.html.twig', array('reservation' => $reservation)))
        ;
        
        $this->mailer->send($message);
    }

}
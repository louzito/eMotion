<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class MailerService
{
    private $mailer;

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    public function sendEmailDeRetard()
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('zito.lou@gmail.com')
            ->setTo('zito.lou@gmail.com')
            ->setBody('Here is the message itself')
        ;

        $mailer->send($message);
    }

}
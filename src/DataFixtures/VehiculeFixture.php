<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 26/09/2017
 * Time: 10:12
 */

namespace Doctrine;


use AppBundle\Entity\User;
use AppBundle\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Date;

class VehiculeFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $vehicule1 = new Vehicule();
        $vehicule2 = new Vehicule();
        $vehicule3 = new Vehicule();
        $vehicule4 = new Vehicule();
        $vehicule5 = new Vehicule();
        $vehicule6 = new Vehicule();

        $vehicule1
        ->setModele("Zoé")
        ->setCouleur("Grise")
        ->setDateAchat(new \DateTime("2013-06-22"))
        ->setMarque("Renault")
        ->setNbKilometres(25110)
        ->setNumSerie("AZS1542D")
        ->setPrixAchat(17000)
        ->setPlaqueImmatriculation("AF-564-KP");
        $vehicule2
            ->setModele("i8")
            ->setCouleur("Bleu")
            ->setDateAchat(new \DateTime("2016-04-18"))
            ->setMarque("BNW")
            ->setNbKilometres(8000)
            ->setNumSerie("BCDFE745")
            ->setPrixAchat(130000)
            ->setPlaqueImmatriculation("MV-647-WP");
        $vehicule3
            ->setModele("Spark")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2017-01-02"))
            ->setMarque("Chevrolet")
            ->setNbKilometres(3600)
            ->setNumSerie("GRDE7469")
            ->setPrixAchat(600)
            ->setPlaqueImmatriculation("WY-679-JD");
        $vehicule4
            ->setModele("Ion")
            ->setCouleur("Bleu")
            ->setDateAchat(new \DateTime("2017-06-25"))
            ->setMarque("Peugeot")
            ->setNbKilometres(1250)
            ->setNumSerie("RQ7DE8ZE")
            ->setPrixAchat(14000)
            ->setPlaqueImmatriculation("PK-555-RD");
        $vehicule5
            ->setModele("S")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2015-12-30"))
            ->setMarque("Tesla")
            ->setNbKilometres(30000)
            ->setNumSerie("QEZ84RD6")
            ->setPrixAchat(76000)
            ->setPlaqueImmatriculation("PQ-251-GF");
        $vehicule6
            ->setModele("i3")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2017-26-09"))
            ->setMarque("BMW")
            ->setNbKilometres(0)
            ->setNumSerie("WZY45FT6")
            ->setPrixAchat(60500)
            ->setPlaqueImmatriculation("XE-666-WS");

        $user1 = new User();



        $manager->flush();

    }
}
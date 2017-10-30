<?php
/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 26/09/2017
 * Time: 10:12
 */

namespace AppBundle\DataFixtures\ORM;


use AppBundle\Entity\User;
use AppBundle\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Date;
use AppBundle\Entity\OffreLocation;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Entity\Image;

class LoadVehicule extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $image1 = new Image();
        $image2 = new Image();
        $image3 = new Image();
        $image4 = new Image();
        $image5 = new Image();
        $image6 = new Image();

        $image1
            ->setUrl("zoe.jpg")
            ->setAlt("zoe.jpg");

        $image2
            ->setUrl("BMW-i8.jpg")
            ->setAlt("BMW-i8.jpg");

        $image3
            ->setUrl("chevrolet-spark.jpg")
            ->setAlt("chevrolet-spark.jpg");

        $image4
            ->setUrl("peugeot_ion.jpg")
            ->setAlt("peugeot_ion.jpg");

        $image5
            ->setUrl("tesla-s.jpg")
            ->setAlt("tesla-s.jpg");

        $image6
            ->setUrl("bmw.jpg")
            ->setAlt("bmw.jpg");


        $vehicule1 = new Vehicule();
        $vehicule2 = new Vehicule();
        $vehicule3 = new Vehicule();
        $vehicule4 = new Vehicule();
        $vehicule5 = new Vehicule();
        $vehicule6 = new Vehicule();

        $vehicule1
            ->setImage($image1)
            ->setModele("Zoé")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2013-06-22"))
            ->setMarque("Renault")
            ->setNbKilometres(25110)
            ->setNumSerie("AZS1542D")
            ->setPrixAchat(17000)
            ->setType('Voiture')
            ->setPlaqueImmatriculation("AF-564-KP");
        $vehicule2
            ->setImage($image2)
            ->setModele("i8")
            ->setCouleur("Bleu")
            ->setDateAchat(new \DateTime("2016-04-18"))
            ->setMarque("BMW")
            ->setNbKilometres(8000)
            ->setNumSerie("BCDFE745")
            ->setType('Voiture')
            ->setPrixAchat(130000)
            ->setPlaqueImmatriculation("MV-647-WP");
        $vehicule3
            ->setImage($image3)
            ->setModele("Spark")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2017-01-02"))
            ->setMarque("Chevrolet")
            ->setType('Voiture')
            ->setNbKilometres(3600)
            ->setNumSerie("GRDE7469")
            ->setPrixAchat(600)
            ->setPlaqueImmatriculation("WY-679-JD");
        $vehicule4
            ->setImage($image4)
            ->setModele("Ion")
            ->setCouleur("Bleu")
            ->setDateAchat(new \DateTime("2017-06-25"))
            ->setMarque("Peugeot")
            ->setNbKilometres(1250)
            ->setType('Voiture')
            ->setNumSerie("RQ7DE8ZE")
            ->setPrixAchat(14000)
            ->setPlaqueImmatriculation("PK-555-RD");
        $vehicule5
            ->setImage($image5)
            ->setModele("S")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2015-12-30"))
            ->setMarque("Tesla")
            ->setNbKilometres(30000)
            ->setType('Voiture')
            ->setNumSerie("QEZ84RD6")
            ->setPrixAchat(76000)
            ->setPlaqueImmatriculation("PQ-251-GF");
        $vehicule6
            ->setImage($image6)
            ->setModele("i3")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2017-01-01"))
            ->setMarque("BMW")
            ->setNbKilometres(0)
            ->setNumSerie("WZY45FT6")
            ->setPrixAchat(60500)
            ->setType('Scooter')
            ->setPlaqueImmatriculation("XE-666-WS");

        for ($i=1;$i<=6;$i++) {
            $location = new OffreLocation();
            $vehicule = 'vehicule'.$i;
            $location->setVehicule($$vehicule);
            $location->setDateDebut(new \DateTime('2017-06-01'));
            $location->setDateFin(new \DateTime('2020-01-01'));
            $location->setPrixJournalier(rand(10, 50));
            $location->setKmJournalier(250);
            $location->setVille('Paris');
            $manager->persist($$vehicule);
            $manager->persist($location);
            $manager->flush();
        }

    }
    public function getOrder(){

        return 9;
    }
}
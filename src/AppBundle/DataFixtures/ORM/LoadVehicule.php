<?php

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
        $image7 = new Image();
        $image8 = new Image();
        $image9 = new Image();
        $image10 = new Image();
        $image11 = new Image();
        $image12 = new Image();

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
    
        $image7
            ->setUrl("govecs-s2.6.jpg")
            ->setAlt("govecs-s2.6.jpg");

        $image8
            ->setUrl("pink-me.jpg")
            ->setAlt("pink-me.jpg");

        $image9
            ->setUrl("govecs-s1.5.jpg")
            ->setAlt("govecs-s1.5.jpg");

        $image10
            ->setUrl("govecs-s3.6.jpg")
            ->setAlt("govecs-s3.6.jpg");

        $image11
            ->setUrl("muvi-50.jpg")
            ->setAlt("muvi-50.jpg");

        $image12
            ->setUrl("super-soco.jpg")
            ->setAlt("super-soco.jpg");

        $vehicule1 = new Vehicule();
        $vehicule2 = new Vehicule();
        $vehicule3 = new Vehicule();
        $vehicule4 = new Vehicule();
        $vehicule5 = new Vehicule();
        $vehicule6 = new Vehicule();
        $vehicule7 = new Vehicule();
        $vehicule8 = new Vehicule();
        $vehicule9 = new Vehicule();
        $vehicule10 = new Vehicule();
        $vehicule11 = new Vehicule();
        $vehicule12 = new Vehicule();

        $vehicule1
            ->setImage($image1)
            ->setModele("Zoé")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2013-06-22"))
            ->setMarque("Renault")
            ->setNbKilometres(25110)
            ->setNumSerie("AZS1542D")
            ->setPrixAchat(17000)
            ->setType('voiture')
            ->setPlaqueImmatriculation("AF-564-KP");
        $vehicule2
            ->setImage($image2)
            ->setModele("i8")
            ->setCouleur("Bleu")
            ->setDateAchat(new \DateTime("2016-04-18"))
            ->setMarque("BMW")
            ->setNbKilometres(8000)
            ->setNumSerie("BCDFE745")
            ->setType('voiture')
            ->setPrixAchat(130000)
            ->setPlaqueImmatriculation("MV-647-WP");
        $vehicule3
            ->setImage($image3)
            ->setModele("Spark")
            ->setCouleur("Grise")
            ->setDateAchat(new \DateTime("2017-01-02"))
            ->setMarque("Chevrolet")
            ->setType('voiture')
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
            ->setType('voiture')
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
            ->setType('voiture')
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
            ->setType('voiture')
            ->setPlaqueImmatriculation("XE-666-WS");

        $vehicule7
            ->setImage($image7)
            ->setModele("Go ! S2.6")
            ->setCouleur("Blanc")
            ->setDateAchat(new \DateTime("2017-03-01"))
            ->setMarque("Govecs")
            ->setNbKilometres(1200)
            ->setNumSerie("WZZ45QJ6")
            ->setPrixAchat(7590)
            ->setType('scooter')
            ->setPlaqueImmatriculation("LK-654-DS");

        $vehicule8
            ->setImage($image8)
            ->setModele("Electric Move")
            ->setCouleur("Rouge")
            ->setDateAchat(new \DateTime("2017-02-01"))
            ->setMarque("Pink me")
            ->setNbKilometres(8500)
            ->setNumSerie("WADC5QJ6")
            ->setPrixAchat(3490)
            ->setType('scooter')
            ->setPlaqueImmatriculation("GF-644-MS");

        $vehicule9
            ->setImage($image9)
            ->setModele("Go ! S1.5")
            ->setCouleur("Blanc")
            ->setDateAchat(new \DateTime("2017-03-01"))
            ->setMarque("Govecs")
            ->setNbKilometres(1200)
            ->setNumSerie("WZ589QJ6")
            ->setPrixAchat(5990)
            ->setType('scooter')
            ->setPlaqueImmatriculation("PL-674-KO");

        $vehicule10
            ->setImage($image10)
            ->setModele("Go ! S3.6")
            ->setCouleur("Blanc")
            ->setDateAchat(new \DateTime("2017-03-01"))
            ->setMarque("Govecs")
            ->setNbKilometres(12700)
            ->setNumSerie("WZ789QJ6")
            ->setPrixAchat(8690)
            ->setType('scooter')
            ->setPlaqueImmatriculation("PI-645-UI");

        $vehicule11
            ->setImage($image11)
            ->setModele("50")
            ->setCouleur("Noir")
            ->setDateAchat(new \DateTime("2017-03-01"))
            ->setMarque("Muvi")
            ->setNbKilometres(12700)
            ->setNumSerie("WZ78QUI6")
            ->setPrixAchat(4690)
            ->setType('scooter')
            ->setPlaqueImmatriculation("QI-813-UI");

        $vehicule12
            ->setImage($image12)
            ->setModele("SUPER SOCO")
            ->setCouleur("Rouge")
            ->setDateAchat(new \DateTime("2017-03-01"))
            ->setMarque("Soco")
            ->setNbKilometres(7500)
            ->setNumSerie("WZJHUUI6")
            ->setPrixAchat(2890)
            ->setType('scooter')
            ->setPlaqueImmatriculation("LO-813-PK");

        for ($i=1;$i<=12;$i++) {
            $location = new OffreLocation();
            $vehicule = 'vehicule'.$i;
            $location->setVehicule($$vehicule);
            $location->setDateDebut(new \DateTime('2017-06-01'));
            $location->setDateFin(new \DateTime('2020-01-01'));
            $location->setPrixJournalier(rand(10, 50));
            $location->setKmJournalier(250);
            if($i%2 == 0){
                $location->setVille('Paris');
            } else {
                $location->setVille('Lyon');
            }
            
            $manager->persist($$vehicule);
            $manager->persist($location);
            $manager->flush();
        }

    }
    public function getOrder(){

        return 9;
    }
}
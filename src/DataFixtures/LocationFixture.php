<?php

/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 26/09/2017
 * Time: 10:15
 */
use Doctrine\Bundle\FixturesBundle\Fixture;
use AppBundle\Entity\OffreLocation;
use Doctrine\Common\Persistence\ObjectManager;
class LocationFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $location = new OffreLocation();
            $location->setName('location '.$i);
            $location->setPrice(mt_rand(10, 100));
            $manager->persist($location);
        }

        $manager->flush();


    }
}
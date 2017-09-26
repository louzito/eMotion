<?php


use Doctrine\Bundle\FixturesBundle\Fixture;
use AppBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 26/09/2017
 * Time: 09:56
 */
class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // create 20 products! Bam!
        for ($i = 0; $i < 20; $i++) {
            $user = new BaseUser ();
            $user->setName('utilisateur '.$i);
            $user->setPrice(mt_rand(10, 100));
            $manager->persist($user);
        }

        $manager->flush();


    }
}

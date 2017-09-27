<?php
namespace AppBundle\DataFixtures\ORM;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Created by PhpStorm.
 * User: Mathieu
 * Date: 26/09/2017
 * Time: 09:56
 */
class LoadUser extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $user1 = new User();
        $user2 = new User();
        $user3 = new User();

        $user1
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail("admin@emotion.com")
            ->setDateDeNaissance(new DateTime("1989-05-12"))
            ->setPassword("admin")
            ->setUsername("admin")
            ->setAdresse("8 rue de Londre")
            ->setCp(75015)
            ->setVille("Paris")
            ->setNom("Pagel")
            ->setPrenom("Maurice")
            ->setNumPermis("5A12DF56")
            ->setTelephone("0689457823")
            ;

        $user2
            ->setRoles(['ROLE_USER'])
            ->setEmail("pierre.dupont@gmail.com")
            ->setDateDeNaissance(new DateTime("1990-08-30"))
            ->setPassword("admin")
            ->setUsername("pierreD")
            ->setAdresse("45 rue des roses")
            ->setCp(75006)
            ->setVille("Paris")
            ->setNom("Dupont")
            ->setPrenom("Pierre")
            ->setNumPermis("D5DF5255")
            ->setTelephone("0645789545");

        $user3
            ->setRoles(['ROLE_USER'])
            ->setEmail("julien.durand@gmail.com")
            ->setDateDeNaissance(new DateTime("1974-05-04"))
            ->setPassword("admin")
            ->setUsername("julienD")
            ->setAdresse("6 rue de la bruyère")
            ->setCp(45000)
            ->setVille("Orléan")
            ->setNom("Durand")
            ->setPrenom("Julien")
            ->setNumPermis("5DFG2645")
            ->setTelephone("0689457820");


        $manager->flush();


    }

    public function getOrder(){

        return 10;
    }
}

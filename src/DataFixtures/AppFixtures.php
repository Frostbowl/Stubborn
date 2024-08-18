<?php

namespace App\Datafixtures;

use App\Entity\User;
use App\Entity\Sweatshirt;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {

        //Création admin et utilisateur

        $admin = new User();
        $admin->setEmail('admin@exemple.re');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setName('admin');
        $admin->setIsVerified(true);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));

        $manager->persist($admin);

        $user = new User();
        $user->setEmail('user@exemple.re');
        $user->setRoles(['ROLE_USER']);
        $user->setName('user');
        $user->setIsVerified(true);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));

        $manager->persist($user);

        //Création de trois produits (sweatshirt)
        $sweatshirt1 = new Sweatshirt();
        $sweatshirt1 -> setName('BlackBelt');
        $sweatshirt1 -> setPrice('29.90');
        $sweatshirt1 -> setStockXS(5);
        $sweatshirt1 -> setStockS(5);
        $sweatshirt1 -> setStockM(5);
        $sweatshirt1 -> setStockL(5);
        $sweatshirt1 -> setStockXL(5);
        $sweatshirt1 -> setImageName('1.jpeg');
        $manager->persist($sweatshirt1);

        $sweatshirt2 = new Sweatshirt();
        $sweatshirt2 -> setName('BueBelt');
        $sweatshirt2 -> setPrice('29.90');
        $sweatshirt2 -> setStockXS(5);
        $sweatshirt2 -> setStockS(5);
        $sweatshirt2 -> setStockM(5);
        $sweatshirt2 -> setStockL(5);
        $sweatshirt2 -> setStockXL(5);
        $sweatshirt2 -> setImageName('2.jpeg');
        $manager->persist($sweatshirt2);

        $sweatshirt3 = new Sweatshirt();
        $sweatshirt3 -> setName('Street');
        $sweatshirt3 -> setPrice('34.50');
        $sweatshirt3 -> setStockXS(5);
        $sweatshirt3 -> setStockS(5);
        $sweatshirt3 -> setStockM(5);
        $sweatshirt3 -> setStockL(5);
        $sweatshirt3 -> setStockXL(5);
        $sweatshirt3 -> setImageName('3.jpeg');
        $manager->persist($sweatshirt3);

        $sweatshirt4 = new Sweatshirt();
        $sweatshirt4 -> setName('PokeBall');
        $sweatshirt4 -> setPrice('45');
        $sweatshirt4 -> setStockXS(5);
        $sweatshirt4 -> setStockS(5);
        $sweatshirt4 -> setStockM(5);
        $sweatshirt4 -> setStockL(5);
        $sweatshirt4 -> setStockXL(5);
        $sweatshirt4 -> setImageName('4.jpeg');
        $manager->persist($sweatshirt4);

        $manager->flush();
        
    }


}
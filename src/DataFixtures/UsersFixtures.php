<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
Use Faker;

class UsersFixtures extends Fixture
{

    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
        ) {
    }
    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@demo.fr');
        $admin->setLastname('Garban');
        $admin->setFirstName('Matt');
        $admin->setAddress('22 rue du code');
        $admin->setZipcode('33333');
        $admin->setCity('Chezoim');
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin, 'admin')
        );
        $admin->setRoles(['ROLE_ADMIN']);


        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

for($usr =1; $usr <=5; $usr++ )
{
    
}
        $manager->flush();
    }
}

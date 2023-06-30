<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Image;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImagesFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private SluggerInterface $slugger) {}


    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for($img=1;$img<=100;$img++)
        {
            $image = new Image();
            $image->setName($faker->image(null,640,480));
            $product=  $this->getReference('prod-'.rand(1, 10));
            $image->setProduct($product);

            $manager->persist($image);
        }

        $manager->flush();
    }

    public function getDependencies():array{
        return [
            ProductsFixtures::class
        ];
    }
}

<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger,) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        for($prod=1;$prod<=10;$prod++)
        {
            $product = new Product();
            $product->setName($faker->text(5));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getName())->lower());
            $product->setPrice($faker->numberBetween(900, 150000));
            $product->setStock($faker->numberBetween(0, 10));
            //on va chercher une référence de catégorie
            $category=$this->getReference('cat-'.rand(1,8));
            $product->setCategories($category);

            $this->setReference('prod-'.$prod, $product);

            $manager->persist($product);
            // $this->addReference('prod-'.$prod, $product);
            $this->setReference('prod-'.$prod, $product);

        }

        $manager->flush();
    }
}

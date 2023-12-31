<?php

namespace App\DataFixtures;

use SNMP;
use App\Entity\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesFixtures extends Fixture
{
    private $counter =1;

    public function __construct(private SluggerInterface $slugger) {}

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', null, $manager);
        $this->createCategory('Ordinateurs portables', $parent, $manager);
        $this->createCategory('Ecrans', $parent, $manager);
        $this->createCategory('Souris', $parent, $manager);

        $parent = $this->createCategory('IMode', null, $manager);
        $this->createCategory('Homme', $parent, $manager);
        $this->createCategory('Femme', $parent, $manager);
        $this->createCategory('Enfant', $parent, $manager);
        

        $manager->flush();
    }

    public function createCategory(string $name, Categorie $parent = null, ObjectManager $manager )
    {
        $category = new Categorie();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getName())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        return $category;
    }
}

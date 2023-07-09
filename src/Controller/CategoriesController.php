<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Categorie;
use App\Repository\ProductRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/categories', name: 'categories_')]
class CategoriesController extends AbstractController
{
    #[Route('/{slug}', name: 'list')]
    public function details(string $slug, CategorieRepository $categorieRepository): Response
    {
        $category = $categorieRepository->findOneBy(['slug' => $slug]);

        //on va chercher la liste dews produits de la catégorie
        $products= $category->getProducts();

        // return $this->render('categories/list.html.twig',compact('category', 'products'));
        return $this->render('categories/list.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }


}

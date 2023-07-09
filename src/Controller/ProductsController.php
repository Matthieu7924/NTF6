<?php

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ProductRepository;


#[Route('/produits', name: 'products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('products/index.html.twig');
    }

    // #[Route('/{slug}', name: 'details')]
    // public function details(Product $product): Response
    // {
    //     dd($product);
    //     return $this->render('products/details.html.twig');
    // }


    #[Route('/{slug}', name: 'details')]
    public function details(string $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        return $this->render('products/details.html.twig',compact('product'));
    }


}

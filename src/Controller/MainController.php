<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CategorieRepository $categoriesRepository): Response
    {
        // $categories = $categoriesRepository->findBy();
        return $this->render('main/index.html.twig',[
            'categories'=> $categoriesRepository->findBy([],['categoryOrder'=>'asc'])
        ]);
    }
}

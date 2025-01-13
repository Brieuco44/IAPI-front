<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodStoreController extends AbstractController
{
    #[Route('/', name: 'app_food_store')]
    public function index(ProductRepository $productRepository): Response
    {
        // Récupérer tous les produits depuis la base de données
        $products = $productRepository->findAll();

        return $this->render('food_store/index.html.twig', [
            'products' => $products,
        ]);
    }
}

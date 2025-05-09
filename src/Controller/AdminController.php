<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin',)]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(Request $request, ProductRepository $productRepository, ReviewRepository $reviewRepository): Response
    {
        $productId = $request->query->get('product');
        $dateOrder = $request->query->get('date', 'desc');

        $products = $productRepository->findAll();

        $criteria = [];
        if ($productId) {
            $criteria['product'] = $productId;
        }

        $reviews = $reviewRepository->findBy($criteria, ['date' => $dateOrder]);

        return $this->render('admin/dashboard.html.twig', [
            'products' => $products,
            'reviews' => $reviews
        ]);
    }
}

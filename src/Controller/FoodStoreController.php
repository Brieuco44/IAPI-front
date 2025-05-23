<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(
        int $id,
        ProductRepository $productRepository,
        ReviewRepository $reviewRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }


        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        $comments = $reviewRepository->findBy(
            ['product' => $product],
            ['date' => 'DESC']
        );

        $total  = array_sum(array_map(fn(Review $c) => $c->getRating(), $comments));
        $count  = count($comments) ?: 1;
        $avg    = $total / $count;

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'You must be logged in to submit a review.');
                return $this->redirectToRoute('app_login'); // Replace with your login route name
            }

            $review->setUser($this->getUser());

            $rating = $request->request->get('rating');
            $review->setRating($rating);

            $review->setProduct($product);
            $review->setDate(new \DateTime());

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }
        $comments = $reviewRepository->findBy(['product' => $product], ['date' => 'DESC']);

        return $this->render('food_store/detail.html.twig', [
            'product'    => $product,
            'comments'   => $comments,
            'form'       => $form->createView(),
            'avgRating'  => $avg,
        ]);
    }
}

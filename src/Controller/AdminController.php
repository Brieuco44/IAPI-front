<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\ReviewRepository;
use App\Service\SentimentsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function dashboard(
        Request $request,
        ProductRepository $productRepository,
        ReviewRepository $reviewRepository,
        SentimentsService $sentimentsService
    ): Response {
        // Fetch filters from query
        $productId   = $request->query->get('product');
        $dateOrder   = $request->query->get('date', 'desc');
        $sentimentFilter = $request->query->get('sentiment');

        // Fetch products for dropdown
        $products = $productRepository->findAll();

        // Base criteria
        $criteria = [];
        if ($productId) {
            $criteria['product'] = $productId;
        }

        // Fetch reviews sorted by date
        $reviews = $reviewRepository->findBy($criteria, ['date' => $dateOrder]);

        // Prepare texts for sentiment analysis
        $texts = array_map(fn($r) => $r->getMessage(), $reviews);
        $sentiments = $sentimentsService->analyzeSentiments($texts);

        // Attach sentiment data back to reviews and filter
        foreach ($reviews as $i => $review) {
            $sentiment = $sentiments[$i] ?? null;
            $review->sentiment = $sentiment;
        }

        // If sentiment filter is set, filter reviews array
        if ($sentimentFilter) {
            $reviews = array_filter($reviews, fn($r) =>
            isset($r->sentiment['label']) && $r->sentiment['label'] === $sentimentFilter
            );
        }

        $counts = ['POSITIVE' => 0, 'NEUTRAL' => 0, 'NEGATIVE' => 0];
        foreach ($sentiments as $sent) {
            if (!empty($sent['label']) && array_key_exists($sent['label'], $counts)) {
                $counts[$sent['label']]++;
            }
        }

        // Pass everything to Twig
        return $this->render('admin/dashboard.html.twig', [
            'products'       => $products,
            'reviews'        => $reviews,
            'stats'          => [
                'positiveCount' => $counts['POSITIVE'],
                'neutralCount'  => $counts['NEUTRAL'],
                'negativeCount' => $counts['NEGATIVE'],
            ],
        ]);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FoodStoreController extends AbstractController
{
    #[Route('/', name: 'app_food_store')]
    public function index(): Response
    {
        // Exemple de données à afficher dans la boutique
        $products = [
            ['name' => 'Pizza Margherita', 'description' => 'Une pizza classique avec une base de tomate et du fromage mozzarella frais.', 'price' => 9.99, 'image' => 'margherita.jpg'],
            ['name' => 'Penne alla Vodka', 'description' => 'Des pâtes Penne servies avec une sauce crémeuse à la vodka.', 'price' => 14.50, 'image' => 'penne_vodka.jpg'],
            ['name' => 'Risotto au champignon', 'description' => 'Un risotto crémeux aux champignons porcini, servi avec du parmesan râpé.', 'price' => 13.00, 'image' => 'risotto.jpg'],
            ['name' => 'Spaghetti Bolognese', 'description' => 'Des spaghetti classiques accompagnés de sauce bolognaise faite maison.', 'price' => 11.50, 'image' => 'spaghetti_bolognese.jpg'],
            ['name' => 'Lasagne', 'description' => 'Des lasagnes traditionnelles, faites avec des feuilles de pâte maison, garnies de bœuf et de sauce béchamel.', 'price' => 15.00, 'image' => 'lasagne.jpg'],
            ['name' => 'Osso Buco', 'description' => 'Un plat italien traditionnel de jarret de veau braisé, servi avec une sauce tomate et du vin blanc.', 'price' => 18.50, 'image' => 'osso_buco.jpg'],
            ['name' => 'Tiramisu', 'description' => 'Un dessert italien classique à base de biscuits imbibés de café, superposé avec une crème mascarpone.', 'price' => 7.99, 'image' => 'tiramisu.jpg'],
            ['name' => 'Cannoli', 'description' => 'Des cannoli italiens traditionnels, garnis de crème de ricotta sucrée et de pépites de chocolat.', 'price' => 6.50, 'image' => 'cannoli.jpg'],
            ['name' => 'Gelato', 'description' => 'Un dessert glacé italien fait maison, disponible en plusieurs saveurs comme vanille, chocolat ou fruits de saison.', 'price' => 5.99, 'image' => 'gelato.jpg'],
            ['name' => 'Caprese Salad', 'description' => 'Une salade fraîche à base de tomates, mozzarella, basilic, huile d’olive et vinaigre balsamique.', 'price' => 9.50, 'image' => 'caprese_salad.jpg'],
        ];


        return $this->render('food_store/index.html.twig', [
            'products' => $products,
        ]);
    }
}

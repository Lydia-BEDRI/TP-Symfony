<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;  // Ajouter le RestaurantRepository
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RestaurantController extends AbstractController
{
    #[Route('/restaurants', name: 'app_restaurant')]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        // Récupère tous les restaurants depuis la base de données
        $restaurants = $restaurantRepository->findAll();

        // Rendre la vue 'restaurant/index.html.twig' en passant la variable restaurants
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,  // Passer la liste des restaurants
        ]);
    }
    #[Route('/restaurant/{id}', name: 'restaurant_show', requirements: ['id' => '\d+'])]
    public function show(int $id, RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = $restaurantRepository->find($id);

        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé');
        }

        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
            'menus' => $restaurant->getMenus(),
        ]);
    }

}

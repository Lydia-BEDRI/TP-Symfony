<?php


namespace App\Controller;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menus', name: 'app_menu')]
    public function index(MenuRepository $menuRepository): Response
    {
        // Récupérer tous les menus
        $menus = $menuRepository->findAll();


        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }
}


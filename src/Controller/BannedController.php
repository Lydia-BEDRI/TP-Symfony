<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BannedController extends AbstractController
{

    #[Route('/banned', name: 'app_banned')]
    #[IsGranted("ROLE_BANNED")]
    public function index(): Response
    {
        return $this->render('banned/index.html.twig', [
            'controller_name' => 'BannedController',
        ]);
    }
}

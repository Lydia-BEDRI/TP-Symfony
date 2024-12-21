<?php


namespace App\Controller;

use App\Repository\TableRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TableController extends AbstractController
{
    #[Route('/menu/{id}/tables', name: 'menu_tables')]
    public function showAvailableTables(
        int                   $id,
        TableRepository       $tableRepository,
        ReservationRepository $reservationRepository
    ): Response
    {
        // Récupérer toutes les tables pour le restaurant du menu donné
        $tables = $tableRepository->findByRestaurantMenu($id);

        // Filtrer les tables non réservées
        $reservedTableIds = $reservationRepository->getReservedTableIds();
        $availableTables = array_filter($tables, function ($table) use ($reservedTableIds) {
            return !in_array($table->getId(), $reservedTableIds);
        });

        return $this->render('table/index.html.twig', [
            'tables' => $availableTables,
        ]);
    }
}


<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Table;
use App\Repository\ReservationRepository;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReservationController extends AbstractController
{
    #[Route('/reservation/{restaurantId}', name: 'reservation_page')]
    #[IsGranted("ROLE_BANNED")]
    public function reservationPage($restaurantId, RestaurantRepository $restaurantRepository): Response
    {
        // Récupérer le restaurant par son ID
        $restaurant = $restaurantRepository->find($restaurantId);

        // Vérifier si le restaurant existe
        if (!$restaurant) {
            throw $this->createNotFoundException('Restaurant non trouvé');
        }

        // Récupérer les menus associés au restaurant
        $menus = $restaurant->getMenus(); // Si la relation entre Restaurant et Menu est définie

        // Passer les données à la vue (restaurant, menus)
        return $this->render('reservation/index.html.twig', [
            'restaurant' => $restaurant,
            'menus' => $menus, // Passer les menus à la vue
        ]);
    }

    #[Route('/reservation/submit/{restaurantId}', name: 'reservation_submit', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function submitReservation(
        Request $request,
                $restaurantId,
        RestaurantRepository $restaurantRepository,
        EntityManagerInterface $entityManager,
        ReservationRepository $reservationRepository
    ): RedirectResponse
    {
        // Récupérer les données du formulaire
        $selectedTableId = $request->request->get('selected_table');
        $reservationDateStr = $request->request->get('reservation_date'); // Format "Y-m-d"
        $reservationTimeStr = $request->request->get('reservation_time'); // Format "H:i"

        // Combiner la date et l'heure en une seule chaîne
        $reservationDateTimeStr = $reservationDateStr . ' ' . $reservationTimeStr;
        $reservationDateTime = \DateTime::createFromFormat('Y-m-d H:i', $reservationDateTimeStr);

        // Vérifier si la création de l'objet DateTime a échoué
        if ($reservationDateTime === false) {
            $this->addFlash('error', 'La date ou l\'heure de réservation sont invalides.');
            return $this->redirectToRoute('reservation_page', ['restaurantId' => $restaurantId]);
        }

        // Récupérer le restaurant pour vérifier l'existence
        $restaurant = $restaurantRepository->find($restaurantId);
        if (!$restaurant) {
            $this->addFlash('error', 'Restaurant non trouvé.');
            return $this->redirectToRoute('reservation_page', ['restaurantId' => $restaurantId]);
        }

        // Trouver la table sélectionnée parmi les tables du restaurant
        $table = null;
        foreach ($restaurant->getTables() as $t) {
            if ($t->getId() == $selectedTableId) {
                $table = $t;
                break;
            }
        }

        if (!$table) {
            $this->addFlash('error', 'Table non trouvée.');
            return $this->redirectToRoute('reservation_page', ['restaurantId' => $restaurantId]);
        }

//        // Vérifier s'il existe déjà une réservation pour la table et la date/heure
//        $existingReservation = $reservationRepository->findOneBy([
//            'table_reserved' => $table,
//            'reservationDateTime' => $reservationDateTime, // Utiliser la nouvelle colonne 'reservationDateTime'
//        ]);
//
//        if ($existingReservation) {
//            $this->addFlash('error', 'Cette table est déjà réservée pour la date et l\'heure choisies.');
//            return $this->redirectToRoute('reservation_page', ['restaurantId' => $restaurantId]);
//        }
//
//        // Créer la réservation avec la nouvelle colonne 'reservation_date_time'
//        $reservation = new Reservation();
//        $reservation->setReservationDateTime($reservationDateTime); // Utiliser reservation_date_time
//        $reservation->setTableReserved($table);
//        $reservation->setRestaurant($restaurant);
//
//        // Enregistrer la réservation
//        $entityManager->persist($reservation);
//        $entityManager->flush();
//
//        // Ajouter un message flash de succès
//        $this->addFlash('success', 'Votre réservation a été effectuée avec succès !');

        // Rediriger l'utilisateur vers une page de résumé
        return $this->redirectToRoute('reservation_summary', ['restaurantId' => $restaurantId]);
    }


    #[Route('/reservation/summary/{restaurantId}', name: 'reservation_summary')]
    public function reservationSummary($restaurantId, ReservationRepository $reservationRepository): Response
    {
        // Récupérer la dernière réservation effectuée pour ce restaurant
        $reservation = $reservationRepository->findOneBy([
            'restaurant' => $restaurantId
        ]);  // Tri par date de réservation, de la plus récente à la plus ancienne

        // Vérifier si une réservation a été trouvée
        if (!$reservation) {
            $this->addFlash('error', 'Aucune réservation trouvée.');
            return $this->redirectToRoute('reservation_page', ['restaurantId' => $restaurantId]);
        }

        // Passer les informations à la vue
        return $this->render('reservation/summary.html.twig', [
            'reservation' => $reservation
        ]);
    }

    #[Route('/reservations', name: 'reservations')]
    #[IsGranted("ROLE_USER")]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
        }

        // Récupérer les réservations de l'utilisateur connecté
        $reservations = $em->getRepository(Reservation::class)->findBy(['user' => $user]);

//       dd($reservations);
        // Passer les réservations à la vue
        return $this->render('reservation/reservations_user.html.twig', [
            'reservations' => $reservations,
        ]);
    }


}
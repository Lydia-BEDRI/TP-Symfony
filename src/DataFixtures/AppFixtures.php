<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Restaurant;
use App\Entity\Reservation;
use App\Entity\Table;
use App\Entity\Menu;
use App\Entity\User;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker
        $faker = Factory::create();

        // Création des utilisateurs
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setPhone($faker->phoneNumber);

            // Assigner les rôles spécifiques
            if ($i === 0) {
                // L'utilisateur 0 a le rôle ROLE_ADMIN
                $user->setRoles(['ROLE_ADMIN']);
            } elseif ($i <= 3) {
                // Les utilisateurs 1 à 3 ont le rôle ROLE_BANNED
                $user->setRoles(['ROLE_BANNED']);
            } else {
                // Le reste des utilisateurs a le rôle ROLE_USER
                $user->setRoles(['ROLE_USER']);
            }

            // Persister l'utilisateur
            $manager->persist($user);

            // Ajouter l'utilisateur au tableau pour les assignations futures
            $users[] = $user;
        }

        // Ne pas oublier de flush à la fin pour enregistrer les utilisateurs
        $manager->flush();

        // Création des restaurants
        for ($i = 0; $i < 5; $i++) {

            $restaurant = new Restaurant();
            $restaurant->setName($faker->company)
                ->setAddress($faker->address)
                // Modification ici pour les horaires
                ->setHours($this->getRandomRestaurantHours())
                ->setImageUrl($faker->imageUrl(640, 480, 'restaurant', true));

            // Création des tables pour chaque restaurant
            for ($j = 0; $j < 10; $j++) {
                $table = new Table();
                $table->setNumber($j + 1)
                    ->setCapacity($faker->numberBetween(2, 8))
                    ->setStatus($faker->randomElement(['available', 'occupied', 'reserved']));
                $restaurant->addTable($table);
                $manager->persist($table);
            }

            // Création des menus pour chaque restaurant
            for ($j = 0; $j < 3; $j++) {
                $menu = new Menu();
                $menu->setName($faker->word)
                    ->setDescription($faker->sentence)
                    ->setPrice($faker->randomFloat(2, 5, 50));
                $restaurant->addMenu($menu);
                $manager->persist($menu);
            }

            // Persister le restaurant avant de créer des réservations
            $manager->persist($restaurant);

            // Création des réservations pour chaque restaurant
            for ($j = 0; $j < 5; $j++) {
                $reservation = new Reservation();
                $reservation->setDate($faker->dateTimeThisMonth)
                    ->setTime(\DateTime::createFromFormat('H:i:s', $faker->time))
                    ->setNumberOfPeople($faker->numberBetween(1, 10))
                    ->setUser($faker->randomElement($users)) // Lier à un utilisateur aléatoire
                    ->setTableReserved($faker->randomElement($restaurant->getTables()->toArray()))
                    ->setRestaurant($restaurant);
                $manager->persist($reservation);
            }
        }

        // Flush une dernière fois pour enregistrer tout dans la base
        $manager->flush();
    }

    // Fonction pour générer des horaires de restaurants avec le format souhaité
    private function getRandomRestaurantHours(): string
    {
        // Plages horaires prédéfinies
        $hours = [
            '8h-15h 16h-23h',
            '9h-17h',
            '10h-18h',
            '12h-20h',
            '6h-14h'
        ];

        // Sélectionner une plage horaire aléatoire
        return $hours[array_rand($hours)];
    }
}

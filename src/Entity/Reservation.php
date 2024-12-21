<?php
namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $reservationDateTime = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "table_reserved_id", referencedColumnName: "id", nullable: false)] // Remarque : "table_reserved_id" doit être le nom de la colonne dans la base de données
    private ?Table $table_reserved = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Restaurant $restaurant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservationDateTime(): ?\DateTimeInterface
    {
        return $this->reservationDateTime;
    }

    public function setReservationDateTime(?\DateTimeInterface $reservationDateTime): self
    {
        $this->reservationDateTime = $reservationDateTime;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getTableReserved(): ?Table
    {
        return $this->table_reserved;
    }

    public function setTableReserved(?Table $table_reserved): self
    {
        $this->table_reserved = $table_reserved;

        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): static
    {
        $this->restaurant = $restaurant;

        return $this;
    }
}

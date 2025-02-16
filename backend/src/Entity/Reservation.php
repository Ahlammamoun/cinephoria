<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $nombreSieges = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $siegesReserves = [];



    #[ORM\Column]
    private ?float $prixTotal = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Seance $seances = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreSieges(): ?int
    {
        return $this->nombreSieges;
    }

    public function setNombreSieges(int $nombreSieges): static
    {
        $this->nombreSieges = $nombreSieges;

        return $this;
    }

    public function getSiegesReserves(): array
    {
        return $this->siegesReserves;
    }

    public function setSiegesReserves(array $siegesReserves): static
    {
        $this->siegesReserves = $siegesReserves;

        return $this;
    }

    public function getPrixTotal(): ?float
    {
        return $this->prixTotal;
    }

    public function setPrixTotal(float $prixTotal): static
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getSeances(): ?Seance
    {
        return $this->seances;
    }

    public function setSeances(?Seance $seances): static
    {
        $this->seances = $seances;

        return $this;
    }
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateReservation = null;
    
    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }
    
    public function setDateReservation(\DateTimeInterface $dateReservation): static
    {
        $this->dateReservation = $dateReservation;
        return $this;
    }
    

}

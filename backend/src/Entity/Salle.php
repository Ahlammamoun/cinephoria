<?php
namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column]
    private ?int $capaciteTotale = null;

    #[ORM\Column]
    private ?int $capacitePMR = null;

    // Relation ManyToOne avec Qualite
    #[ORM\ManyToOne(inversedBy: 'salles')]
    #[ORM\JoinColumn(name: "qualite_id", referencedColumnName: "id", nullable: true)]
    private ?Qualite $qualite = null;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Seance::class)]
    private Collection $seances;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Incident::class)]
    private Collection $incidents;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->incidents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;
        return $this;
    }

    public function getCapaciteTotale(): ?int
    {
        return $this->capaciteTotale;
    }

    public function setCapaciteTotale(int $capaciteTotale): static
    {
        $this->capaciteTotale = $capaciteTotale;
        return $this;
    }

    public function getCapacitePMR(): ?int
    {
        return $this->capacitePMR;
    }

    public function setCapacitePMR(int $capacitePMR): static
    {
        $this->capacitePMR = $capacitePMR;
        return $this;
    }

    public function getQualite(): ?Qualite
    {
        return $this->qualite;
    }

    public function setQualite(?Qualite $qualite): static
    {
        $this->qualite = $qualite;
        return $this;
    }
}
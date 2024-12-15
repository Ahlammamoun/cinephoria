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

    #[ORM\ManyToOne(inversedBy: 'salles')]
    #[ORM\JoinColumn(name: "qualite_id", referencedColumnName: "id", nullable: true)]
    private ?Qualite $qualite = null;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Seance::class)]
    private Collection $seances;

    #[ORM\OneToMany(mappedBy: 'salle', targetEntity: Incident::class)]
    private Collection $incidents;

    #[ORM\ManyToOne(inversedBy: 'salle')]
    private ?Cinema $cinema = null;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->incidents = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    // Getter et setter pour seances
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): self
    {
        if (!$this->seances->contains($seance)) {
            $this->seances->add($seance);
            $seance->setSalle($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): self
    {
        if ($this->seances->removeElement($seance)) {
            if ($seance->getSalle() === $this) {
                $seance->setSalle(null);
            }
        }

        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): static
    {
        $this->cinema = $cinema;

        return $this;
    }
    
    public function getNumero(): ?int
    {
        return $this->numero;
    }

    // Setter pour numero (facultatif)
    public function setNumero(int $numero): self
    {
        $this->numero = $numero;
        return $this;
    }




    public function getCapaciteTotale(): ?int
    {
        return $this->capaciteTotale;
    }

    // Setter pour numero (facultatif)
    public function setCapaciteTotale(int $capaciteTotale): self
    {
        $this->capaciteTotale = $capaciteTotale;
        return $this;
    }

}

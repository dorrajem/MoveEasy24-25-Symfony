<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{   #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idStation", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de la station est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: "Le nom de la station doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de la station ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $nomStation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut de la station est obligatoire.")]
    #[Assert\Choice(
        choices: ['active', 'inactive', 'en maintenance'],
        message: "Le statut doit être 'active', 'inactive' ou 'en maintenance'."
    )]
    private ?string $statut = null;


    
    #[ORM\ManyToOne(targetEntity: Itineraire::class)]
    #[ORM\JoinColumn(name: "itineraire_id", referencedColumnName: "idItineraire", nullable: false)]
    #[Assert\NotNull(message: "L'itinéraire associé est obligatoire.")]
    private ?Itineraire $itineraire = null;
    public function __construct()
    {
        $this->idItineraire = new ArrayCollection();
    }

 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getNomStation(): ?string
    {
        return $this->nomStation;
    }

    public function setNomStation(string $nomStation): static
    {
        $this->nomStation = $nomStation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, itineraire>
     */
    public function getIdItineraire(): Collection
    {
        return $this->idItineraire;
    }
    public function setIdItineraire(Collection $idItineraire): static
    {
        foreach ($idItineraire as $itineraire) {
            if (!$this->idItineraire->contains($itineraire)) {
                $this->idItineraire->add($itineraire);
            }
        }

        return $this;
    }

    public function addIdItineraire(itineraire $idItineraire): static
    {
        if (!$this->id->contains($idItineraire)) {
            $this->idItineraire->add($idItineraire);
        }

        return $this;
    }

    public function removeIdItineraire(itineraire $idItineraire): static
    {
        $this->idItineraire->removeElement($idItineraire);

        return $this;
    }
}

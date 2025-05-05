<?php

namespace App\Entity;

use App\Repository\ItineraireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ItineraireRepository::class)]
class Itineraire
{ 
    

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idItineraire", type: "integer")]
    private ?int $id = null;
 
     #[ORM\Column(type: "string", length: 255)]
     #[Assert\NotBlank(message: "Le nom de l'itinéraire est obligatoire.")]
     #[Assert\Length(
         min: 3,
         max: 255,
         minMessage: "Le nom doit contenir au moins {{ limit }} caractères.",
         maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères."
     )]
     private ?string $nomItineraire = null;
 
     #[ORM\Column(type: "integer")]
     #[Assert\NotNull(message: "La distance est obligatoire.")]
     #[Assert\Positive(message: "La distance doit être un nombre positif.")]
     private ?int $distance = null;
 
     #[ORM\Column(type: Types::TIME_MUTABLE)]
     #[Assert\NotBlank(message: "L'heure de départ prévue est obligatoire.")]
     private ?\DateTimeInterface $heureDepartPrevue = null;
 
     #[ORM\Column(type: Types::TIME_MUTABLE)]
     #[Assert\NotBlank(message: "L'heure d'arrivée prévue est obligatoire.")]
     private ?\DateTimeInterface $heureArriveePrevue = null;
 
     #[ORM\Column(type: Types::DATE_MUTABLE)]
     #[Assert\NotBlank(message: "La date de l'itinéraire est obligatoire.")]
     #[Assert\GreaterThan("yesterday", message: "La date de l'itinéraire doit être au moins aujourd'hui.")]
     private ?\DateTimeInterface $dateiti = null;
 

     #[ORM\ManyToOne(targetEntity: Station::class)]
     #[ORM\JoinColumn(name: "startStat", referencedColumnName: "idStation", nullable: true, onDelete: "CASCADE")]
     private ?Station $startStation = null;
     
     #[ORM\ManyToOne(targetEntity: Station::class)]
     #[ORM\JoinColumn(name: "endStat", referencedColumnName: "idStation", nullable: true, onDelete: "CASCADE")]
     private ?Station $endStation = null;

    
    public function __construct()
    {
        $this->stat = new ArrayCollection();
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

    public function getStartStation(): ?Station
    {
        return $this->startStation;
    }
    public function setStartStation(?Station $startStation): static
    {
        $this->startStation = $startStation;

        return $this;
    }
    public function getEndStation(): ?Station
    {
        return $this->endStation;
    }   
    public function setEndStation(?Station $endStation): static
    {
        $this->endStation = $endStation;

        return $this;
    }
    public function getNomItineraire(): ?string
    {
        return $this->nomItineraire;
    }

    public function setNomItineraire(string $nomItineraire): static
    {
        $this->nomItineraire = $nomItineraire;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(int $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getHeureDepartPrevue(): ?\DateTimeInterface
    {
        return $this->heureDepartPrevue;
    }

    public function setHeureDepartPrevue(\DateTimeInterface $heureDepartPrevue): static
    {
        $this->heureDepartPrevue = $heureDepartPrevue;

        return $this;
    }

    public function getHeureArriveePrevue(): ?\DateTimeInterface
    {
        return $this->heureArriveePrevue;
    }

    public function setHeureArriveePrevue(\DateTimeInterface $heureArriveePrevue): static
    {
        $this->heureArriveePrevue = $heureArriveePrevue;

        return $this;
    }

    public function getDateiti(): ?\DateTimeInterface
    {
        return $this->dateiti;
    }

    public function setDateiti(\DateTimeInterface $dateiti): static
    {
        $this->dateiti = $dateiti;

        return $this;
    }

  
}

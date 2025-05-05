<?php

namespace App\Entity;

use App\Repository\StationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StationRepository::class)]
class Station
{
    #[ORM\Id]
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
        choices: ['Active', 'Inactive', 'En maintenance'],
        message: "Le statut doit être 'Active', 'Inactive' ou 'En Maintenance'."
    )]
    private ?string $statut = null;



    #[ORM\Column(type: 'float', nullable: true)]
private ?float $latitude = null;

#[ORM\Column(type: 'float', nullable: true)]
private ?float $longitude = null;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getLatitude(): ?float
{
    return $this->latitude;
}

public function setLatitude(?float $latitude): self
{
    $this->latitude = $latitude;
    return $this;
}

public function getLongitude(): ?float
{
    return $this->longitude;
}

public function setLongitude(?float $longitude): self
{
    $this->longitude = $longitude;
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

   
}
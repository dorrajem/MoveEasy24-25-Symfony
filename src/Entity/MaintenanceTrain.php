<?php

namespace App\Entity;

use App\Repository\MaintenanceTrainRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Train;

#[ORM\Entity(repositoryClass: MaintenanceTrainRepository::class)]
#[ORM\Table(name: "maintenancetrain")] // ✅ correct table name (no underscore)
class MaintenanceTrain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idMaintenance", type: "integer")] // ✅ match database column exactly
    private ?int $idMaintenance = null;

    #[ORM\ManyToOne(targetEntity: Train::class, inversedBy: 'maintenances')]
    #[ORM\JoinColumn(name: "idTrain", referencedColumnName: "idTrain", nullable: false)] // ✅ correct foreign key
    #[Assert\NotNull(message: "Le train est obligatoire.")]
    private ?Train $train = null;

    #[ORM\Column(name: "dateMaintenance", type: Types::DATE_MUTABLE)] // ✅ match database column exactly
    #[Assert\NotNull(message: "La date de maintenance est obligatoire.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "Veuillez entrer une date valide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de maintenance ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $dateMaintenance = null;

    #[ORM\Column(name: "description", type: Types::TEXT)] // ✅ name specified (safe)
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        min: 5,
        minMessage: "La description doit contenir au moins {{ limit }} caractères.",
        max: 1000,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(name: "statut", type: "string", length: 50, options: ["default" => "Planifiée"])] // ✅ match database
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    #[Assert\Choice(choices: ["Planifiée", "En cours", "Terminée"], message: "Statut invalide.")]
    private ?string $statut = "Planifiée";

    // --- Getters & Setters ---

    public function getIdMaintenance(): ?int
    {
        return $this->idMaintenance;
    }

    public function getTrain(): ?Train
    {
        return $this->train;
    }

    public function setTrain(?Train $train): self
    {
        $this->train = $train;
        return $this;
    }

    public function getDateMaintenance(): ?\DateTimeInterface
    {
        return $this->dateMaintenance;
    }

    public function setDateMaintenance(?\DateTimeInterface $dateMaintenance): self
    {
        $this->dateMaintenance = $dateMaintenance;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }
}

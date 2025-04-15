<?php

namespace App\Entity;

use App\Repository\TrainRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TrainRepository::class)]
class Train
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idTrain", type: "integer")]
    private ?int $idTrain = null;

    #[ORM\Column(name: "TypeTrain", length: 255)]
    #[Assert\NotBlank(message: "Le type de train est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: "Le type de train doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le type de train ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $TypeTrain = null;

    #[ORM\Column(name: "capacite")]
    #[Assert\NotNull(message: "La capacité est obligatoire.")]
    #[Assert\Positive(message: "La capacité doit être un nombre positif.")]
    private ?int $capacite = null;

    #[ORM\Column(name: "statut", length: 50)]
    #[Assert\NotBlank(message: "Le statut est obligatoire.")]
    #[Assert\Choice(choices: ["En service", "Hors service", "En maintenance"], message: "Statut invalide.")]
    private ?string $statut = null;

    #[ORM\Column(name: "dateMiseEnService", type: Types::DATE_MUTABLE)]
    #[Assert\NotNull(message: "La date de mise en service est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de mise en service ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $dateMiseEnService = null;

    #[ORM\OneToMany(mappedBy: "train", targetEntity: MaintenanceTrain::class, cascade: ['persist', 'remove'])]
    private Collection $maintenances;

    public function __construct()
    {
        $this->maintenances = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->idTrain;
    }

    public function getIdTrain(): ?int
    {
        return $this->idTrain;
    }

    public function setIdTrain(?int $idTrain): static
    {
        $this->idTrain = $idTrain;
        return $this;
    }

    public function getTypeTrain(): ?string
    {
        return $this->TypeTrain;
    }

    public function setTypeTrain(?string $TypeTrain): static
    {
        $this->TypeTrain = $TypeTrain;
        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(?int $capacite): static
    {
        $this->capacite = $capacite;
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

    public function getDateMiseEnService(): ?\DateTimeInterface
    {
        return $this->dateMiseEnService;
    }

    public function setDateMiseEnService(?\DateTimeInterface $dateMiseEnService): static
    {
        $this->dateMiseEnService = $dateMiseEnService;
        return $this;
    }

    /** @return Collection<int, MaintenanceTrain> */
    public function getMaintenances(): Collection
    {
        return $this->maintenances;
    }

    public function addMaintenance(MaintenanceTrain $maintenance): static
    {
        if (!$this->maintenances->contains($maintenance)) {
            $this->maintenances[] = $maintenance;
            $maintenance->setTrain($this);
        }
        return $this;
    }

    public function removeMaintenance(MaintenanceTrain $maintenance): static
    {
        if ($this->maintenances->removeElement($maintenance)) {
            if ($maintenance->getTrain() === $this) {
                $maintenance->setTrain(null);
            }
        }
        return $this;
    }
}
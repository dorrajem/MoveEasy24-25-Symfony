<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
   
    #[ORM\Column(name:"idEquipement",nullable: false)]
    private ?int $idEquipement = null;

    #[ORM\Column(name: "idTrain", type: "integer", nullable: true)]
    #[Assert\NotNull(message: "Le train associé ne peut pas être vide.")]
    #[Assert\GreaterThan(
        value: 0,
        message: "Le train associé doit être strictement supérieure à 0."
    )]
    private ?int $idTrain = null;


    #[ORM\Column(name: "typeEquipement", type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Le type d'équipement est requis.")]
    private ?string $typeEquipement = null;

    #[ORM\Column(name: "quantiteDisponible", type: "integer", nullable: true)]
    #[Assert\NotNull(message: 'La quantité disponible est requise.')]
    #[Assert\GreaterThan(value: 0, message: 'La quantité doit être strictement supérieure à 0.')]
    private ?int $quantiteDisponible = null;
    

    #[ORM\Column(name: "statut", type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'Le statut est requis.')]
    #[Assert\Choice(choices: ['Disponible', 'Non disponible','disponible','non disponible'], message: 'Le statut doit être "Disponible" ou "Non disponible".')]
    private ?string $statut = null;



    #[ORM\OneToMany(mappedBy: 'idEquipement', targetEntity: MaintenanceEq::class, cascade: ['remove'])]
    private Collection $maintenances;
    
    public function __construct()
    {
        $this->maintenances = new ArrayCollection();
    }



    
    public function getIdEquipement(): ?int
    {
        return $this->idEquipement;
    }

    public function setIdEquipement(int $idEquipement): static
    {
        $this->idEquipement = $idEquipement;

        return $this;
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

    public function getTypeEquipement(): ?string
    {
        return $this->typeEquipement;
    }

    public function setTypeEquipement(?string $typeEquipement): static
    {
        $this->typeEquipement = $typeEquipement;

        return $this;
    }

    public function getQuantiteDisponible(): ?int
    {
        return $this->quantiteDisponible;
    }

    public function setQuantiteDisponible(?int $quantiteDisponible): static
    {
        $this->quantiteDisponible = $quantiteDisponible;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, MaintenanceEq>
     */
    public function getMaintenances(): Collection
    {
        return $this->maintenances;
    }

    public function addMaintenance(MaintenanceEq $maintenance): static
    {
        if (!$this->maintenances->contains($maintenance)) {
            $this->maintenances->add($maintenance);
            $maintenance->setIdEquipement($this);
        }

        return $this;
    }

    public function removeMaintenance(MaintenanceEq $maintenance): static
    {
        if ($this->maintenances->removeElement($maintenance)) {
            // set the owning side to null (unless already changed)
            if ($maintenance->getIdEquipement() === $this) {
                $maintenance->setIdEquipement(null);
            }
        }

        return $this;
    }

    
}

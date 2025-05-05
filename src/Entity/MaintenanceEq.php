<?php

namespace App\Entity;

use App\Repository\MaintenanceEqRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: MaintenanceEqRepository::class)]
#[ORM\Table(name: "maintenanceEq")]
class MaintenanceEq
{
    #[ORM\Id]
    #[ORM\GeneratedValue]

    #[ORM\Column(name: "idMaintenance")]
    private ?int $idMaintenance = null;

    #[ORM\ManyToOne(inversedBy: 'maintenances')]
    #[ORM\JoinColumn(name: "idEquipement", referencedColumnName: "idEquipement", nullable: false, onDelete: "CASCADE")]
    #[Assert\NotNull(message: "L'équipement associé est requis.")]
    private ?Equipement $idEquipement = null;


    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La description est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $description = null;



    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La période est obligatoire.")]
    #[Assert\Length(
        max: 255,
        maxMessage: "La période ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $periode = null;




   

    #[ORM\Column(name: "dateMaintenance", type: Types::DATE_MUTABLE, nullable: false)]
    #[Assert\NotBlank(message: "La date de maintenance est obligatoire.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date de maintenance doit être aujourd'hui ou dans le futur.")]
    #[Assert\LessThanOrEqual("+1 year", message: "La date de maintenance ne peut pas dépasser un an à partir d'aujourd'hui.")]
    private ?\DateTimeInterface $dateMaintenance = null;

  


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMaintenance(): ?int
    {
        return $this->idMaintenance;
    }

    public function setIdMaintenance(int $idMaintenance): static
    {
        $this->idMaintenance = $idMaintenance;

        return $this;
    }

    public function getIdEquipement(): ?Equipement
    {
        return $this->idEquipement;
    }

    public function setIdEquipement(?Equipement $idEquipement): static
    {
        $this->idEquipement = $idEquipement;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPeriode(): ?string
    {
        return $this->periode;
    }

    public function setPeriode(?string $periode): static
    {
        $this->periode = $periode;

        return $this;
    }

    public function getDateMaintenance(): ?\DateTimeInterface
    {
        return $this->dateMaintenance;
    }

    public function setDateMaintenance(?\DateTimeInterface $dateMaintenance): static
    {
        $this->dateMaintenance = $dateMaintenance;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idR")]
    private ?int $idR = null;

    #[ORM\Column(name: "iduser")]
    private ?int $iduser = null;

    #[ORM\Column(name: "idTrain")]
    private ?int $idTrain = null;

    #[ORM\Column(name: "idItiner")]
    private ?int $idItiner = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, name: "dateR")]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date doit être aujourd'hui ou dans le futur"
    )]
    private ?\DateTimeInterface $dateR = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, name: "heureR")]
    #[Assert\NotBlank(message: "L'heure est obligatoire")]
    private ?\DateTimeInterface $heureR = null;

    #[ORM\Column(length: 50, name: "etatR")]
    #[Assert\NotBlank(message: "L'état de la réservation est obligatoire")]
    private ?string $etatR = null;

    #[ORM\Column(name: "nbrplace")]
    #[Assert\NotBlank(message: "Le nombre de places est obligatoire")]
    #[Assert\Range(
        min: 1,
        max: 255,
        notInRangeMessage: "Le nombre de places doit être entre {{ min }} et {{ max }}"
    )]
    private ?int $nbrplace = null;

    #[ORM\Column(length: 50, name: "typeplace")]
    #[Assert\NotBlank(message: "Le type de place est obligatoire")]
    private ?string $typeplace = null;

    #[ORM\Column(length: 50, name: "typeTrainR")]
    #[Assert\NotBlank(message: "Le type de train est obligatoire")]
    private ?string $typeTrainR = null;

    #[ORM\Column(length: 50, name: "destinationR")]
    #[Assert\NotBlank(message: "La destination est obligatoire")]
    #[Assert\Choice(
        choices: ["Tunis-Sousse", "Tunis-Sfax", "Tunis-Keff"],
        message: "Veuillez choisir une destination valide"
    )]
    private ?string $destinationR = null;

    #[ORM\Column(length: 50, name: "NameUser")]
    #[Assert\NotBlank(message: "Le nom d'utilisateur est obligatoire")]
    private ?string $NameUser = null;
    
    #[ORM\OneToOne(mappedBy: "reservation", targetEntity: Paiement::class, cascade: ["persist", "remove"])]
    private ?Paiement $paiement = null;
    
    // Constructor with default values
    public function __construct()
    {
        $this->etatR = 'En attente';
        $this->typeplace = 'Standard';
        $this->typeTrainR = 'TGV';
    }

    public function getIdR(): ?int
    {
        return $this->idR;
    }

    public function setIdR(int $idR): static
    {
        $this->idR = $idR;

        return $this;
    }

    public function getIduser(): ?int
    {
        return $this->iduser;
    }

    public function setIduser(int $iduser): static
    {
        $this->iduser = $iduser;

        return $this;
    }

    public function getIdTrain(): ?int
    {
        return $this->idTrain;
    }

    public function setIdTrain(int $idTrain): static
    {
        $this->idTrain = $idTrain;

        return $this;
    }

    public function getIdItiner(): ?int
    {
        return $this->idItiner;
    }

    public function setIdItiner(int $idItiner): static
    {
        $this->idItiner = $idItiner;

        return $this;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->dateR;
    }

    public function setDateR(\DateTimeInterface $dateR): static
    {
        $this->dateR = $dateR;

        return $this;
    }

    public function getHeureR(): ?\DateTimeInterface
    {
        return $this->heureR;
    }

    public function setHeureR(\DateTimeInterface $heureR): static
    {
        $this->heureR = $heureR;

        return $this;
    }

    public function getEtatR(): ?string
    {
        return $this->etatR;
    }

    public function setEtatR(string $etatR): static
    {
        $this->etatR = $etatR;

        return $this;
    }

    public function getNbrplace(): ?int
    {
        return $this->nbrplace;
    }

    public function setNbrplace(int $nbrplace): static
    {
        $this->nbrplace = $nbrplace;

        return $this;
    }

    public function getTypeplace(): ?string
    {
        return $this->typeplace;
    }

    public function setTypeplace(string $typeplace): static
    {
        $this->typeplace = $typeplace;

        return $this;
    }

    public function getTypeTrainR(): ?string
    {
        return $this->typeTrainR;
    }

    public function setTypeTrainR(string $typeTrainR): static
    {
        $this->typeTrainR = $typeTrainR;

        return $this;
    }

    public function getDestinationR(): ?string
    {
        return $this->destinationR;
    }

    public function setDestinationR(string $destinationR): static
    {
        $this->destinationR = $destinationR;

        return $this;
    }

    public function getNameUser(): ?string
    {
        return $this->NameUser;
    }

    public function setNameUser(string $NameUser): static
    {
        $this->NameUser = $NameUser;

        return $this;
    }

    public function getPaiement(): ?Paiement
    {
        return $this->paiement;
    }
    
    public function setPaiement(?Paiement $paiement): static
    {
        // unset the owning side of the relation if necessary
        if ($paiement === null && $this->paiement !== null) {
            $this->paiement->setReservation(null);
        }
        
        // set the owning side of the relation if necessary
        if ($paiement !== null && $paiement->getReservation() !== $this) {
            $paiement->setReservation($this);
        }
        
        $this->paiement = $paiement;
        
        return $this;
    }
}
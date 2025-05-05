<?php

namespace App\Entity;

use App\Repository\PaiementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaiementRepository::class)]
class Paiement
{
   
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idP")]
    private ?int $idP = null;

    #[ORM\Column(name: "idRes")]  // Explicitly setting the column name
    private ?int $idRes = null;

    #[ORM\OneToOne(inversedBy: "paiement", targetEntity: Reservation::class)]
    #[ORM\JoinColumn(name: "idRes", referencedColumnName: "idR")]
    private ?Reservation $reservation = null;

    #[ORM\Column(name: "methodeP", length: 50)]
    private ?string $methodeP = null;

    #[ORM\Column(name: "statutP", length: 50)]
    private ?string $statutP = null;

   

    public function getIdP(): ?int
    {
        return $this->idP;
    }

    public function setIdP(int $idP): static
    {
        $this->idP = $idP;

        return $this;
    }

    public function getIdRes(): ?int
    {
        return $this->idRes;
    }

    public function setIdRes(int $idRes): static
    {
        $this->idRes = $idRes;

        return $this;
    }

    public function getMethodeP(): ?string
    {
        return $this->methodeP;
    }

    public function setMethodeP(string $methodeP): static
    {
        $this->methodeP = $methodeP;

        return $this;
    }

    public function getStatutP(): ?string
    {
        return $this->statutP;
    }

    public function setStatutP(string $statutP): static
    {
        $this->statutP = $statutP;

        return $this;
    }
    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }
    
    public function setReservation(?Reservation $reservation): static
    {
        $this->reservation = $reservation;
        
        // Update idRes field when setting the reservation
        $this->idRes = $reservation ? $reservation->getIdR() : null;
        
        return $this;
    }
}

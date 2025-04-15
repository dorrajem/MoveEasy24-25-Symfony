<?php

namespace App\Entity;

use App\Repository\ProfilUtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProfilUtilisateurRepository::class)]
#[ORM\Table(name: "profil_utilisateur")]
class ProfilUtilisateur {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    // Relation OneToOne (côté propriétaire) avec Utilisateur
    #[ORM\OneToOne(targetEntity: Utilisateur::class, inversedBy: "profil")]
    #[ORM\JoinColumn(name: "id_utilisateur", referencedColumnName: "id_utilisateur", nullable: false)]
    private ?Utilisateur $utilisateur = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255, 
        maxMessage: "L'adresse ne peut dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9\s\-\,\.\/\'\"\°]+$/", 
        message: "L'adresse contient des caractères non autorisés."
    )]
    private ?string $adresse = null;
    
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(
        max: 20, 
        maxMessage: "Le téléphone ne peut dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^\+?[0-9\s\-\.()]+$/", 
        message: "Le numéro de téléphone n'est pas valide. Formats acceptés: +33612345678, 0612345678, etc."
    )]
    private ?string $telephone = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255, 
        maxMessage: "La photo de profil ne peut dépasser {{ limit }} caractères."
    )]
    private ?string $photoProfil = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }
    
    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;
        
        // Optionnel : Assurer la cohérence de la relation bidirectionnelle
        if ($utilisateur !== null && $utilisateur->getProfil() !== $this) {
            $utilisateur->setProfil($this);
        }
                
        return $this;
    }
    
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }
    
    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse ? trim($adresse) : null;
        return $this;
    }
    
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }
    
    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone ? trim($telephone) : null;
        return $this;
    }
    
    public function getPhotoProfil(): ?string
    {
        return $this->photoProfil;
    }
    
    public function setPhotoProfil(?string $photoProfil): static
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }
}
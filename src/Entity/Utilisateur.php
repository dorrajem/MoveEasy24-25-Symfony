<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
//use App\Entity\ProfilUtilisateur;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: "utilisateur")]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "id_utilisateur", type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom ne peut dépasser {{ limit }} caractères.")]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Length(max: 100, maxMessage: "Le prénom ne peut dépasser {{ limit }} caractères.")]
    private ?string $prenom = null;

    // Champ ENUM('Admin', 'Employé')
    #[ORM\Column(length: 100, columnDefinition: "ENUM('Admin', 'Employé') NOT NULL")]
    #[Assert\NotBlank(message: "Le rôle est obligatoire.")]
    #[Assert\Choice(choices: ["Admin", "Employé"], message: "Le rôle doit être 'Admin' ou 'Employé'.")]
    private ?string $role = null;
    

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'email n'est pas valide.")]
    private ?string $email = null;

    #[ORM\Column(name: "mot_de_passe", length: 255)]
    #[Assert\NotBlank(message: "Le mot de passe est obligatoire.")]
    #[Assert\Length(min: 6, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.")]
    private ?string $motDePasse = null;

    // Relation OneToOne (côté inverse)
    //#[ORM\OneToOne(targetEntity: ProfilUtilisateur::class, mappedBy: "utilisateur", cascade: ["persist", "remove"])]
    //private ?ProfilUtilisateur $profil = null;

    // ------------------ GETTERS & SETTERS ------------------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

   /* public function getProfil(): ?ProfilUtilisateur
    {
        return $this->profil;
    }

    public function setProfil(?ProfilUtilisateur $profil): static
    {
        $this->profil = $profil;

        // Assurer la cohérence de la relation bidirectionnelle
        if ($profil !== null && $profil->getUtilisateur() !== $this) {
            $profil->setUtilisateur($this);
        }
        return $this;
    
}*/
}
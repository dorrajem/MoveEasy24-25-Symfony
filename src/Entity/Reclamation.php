<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    /**
 * @ORM\ManyToOne(targe tEntity=User::class, inversedBy="reclamations")
 */
private $user;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "La description doit contenir au moins {{ limit }} caractères",
        maxMessage: "La description ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Veuillez sélectionner une catégorie")]
    #[Assert\Choice(
        choices: ["Produit", "Service", "Livraison", "Support", "Autre"],
        message: "Catégorie invalide, veuillez sélectionner une option valide"
    )]
    private ?string $categorie = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message: "La date de soumission est requise")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être valide")]
    private \DateTimeInterface $date_soumission;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le statut ne peut pas être vide")]
    #[Assert\Choice(
        choices: ["Nouvelle", "En cours", "Traitée", "Clôturée", "En attente"],
        message: "Statut invalide, veuillez sélectionner un statut valide"
    )]
    private ?string $statut = 'Nouvelle'; // Changed default value to match form choices

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message: "L'identifiant utilisateur est requis")]
    #[Assert\PositiveOrZero(message: "L'identifiant utilisateur doit être un nombre positif ou zéro")]
    private ?int $id_utilisateur = null;
    
    #[ORM\Column(length: 1000, nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: "La réponse ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $reponse = null;

    public function __construct()
    {
        $this->date_soumission = new \DateTime(); // set current date by default
        $this->statut = 'Nouvelle';
    }

    // Getter and setter methods

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getDateSoumission(): ?\DateTimeInterface
    {
        return $this->date_soumission;
    }

    public function setDateSoumission(\DateTimeInterface $dateSoumission): static
    {
        $this->date_soumission = $dateSoumission;

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

    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(int $id_utilisateur): static
    {
        $this->id_utilisateur = $id_utilisateur;

        return $this;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(?string $reponse): static
    {
        $this->reponse = $reponse;
        return $this;
    }
}
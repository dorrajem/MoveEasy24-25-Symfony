<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    
    #[ORM\OneToMany(mappedBy: 'avis', targetEntity: AvisReaction::class, orphanRemoval: true)]
    private Collection $reactions;

    public function __construct()
    {
        $this->reactions = new ArrayCollection();
        $this->dateAvis = new \DateTime();
    }
    /**
     * @return Collection<int, AvisReaction>
     */
    public function getReactions(): Collection
    {
        return $this->reactions;
    }

    public function addReaction(AvisReaction $reaction): static
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
            $reaction->setAvis($this);
        }

        return $this;
    }

    public function removeReaction(AvisReaction $reaction): static
    {
        if ($this->reactions->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getAvis() === $this) {
                $reaction->setAvis(null);
            }
        }

        return $this;
    }

    /**
     * Compte le nombre de likes
     */
    public function getLikesCount(): int
    {
        return $this->reactions->filter(fn(AvisReaction $reaction) => $reaction->isLike() === true)->count();
    }

    /**
     * Compte le nombre de dislikes
     */
    public function getDislikesCount(): int
    {
        return $this->reactions->filter(fn(AvisReaction $reaction) => $reaction->isLike() === false)->count();
    }

    /**
     * Vérifie si un utilisateur a déjà liké
     */
    public function isLikedByUser(int $userId): bool
    {
        foreach ($this->reactions as $reaction) {
            if ($reaction->getUtilisateurId() === $userId && $reaction->isLike() === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * Vérifie si un utilisateur a déjà disliké
     */
    public function isDislikedByUser(int $userId): bool
    {
        foreach ($this->reactions as $reaction) {
            if ($reaction->getUtilisateurId() === $userId && $reaction->isLike() === false) {
                return true;
            }
        }
        return false;
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le contenu de l'avis ne peut pas être vide")]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: "Le contenu doit faire au moins {{ limit }} caractères",
        maxMessage: "Le contenu ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $contenu = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez attribuer une note")]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: "La note doit être comprise entre {{ min }} et {{ max }}"
    )]
    private ?int $note = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date ne peut pas être dans le passé"
    )]
    private ?\DateTimeInterface $dateAvis = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "L'ID utilisateur est obligatoire")]
    #[Assert\Positive(message: "L'ID utilisateur doit être un nombre positif")]
    private ?int $id_utilisateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRatingDescription(int $rating): string
    {
        $descriptions = [
            1 => 'Très insatisfait - Mauvaise expérience',
            2 => 'Insatisfait - Quelques problèmes',
            3 => 'Moyen - Correct mais peut mieux faire',
            4 => 'Satisfait - Bonne expérience',
            5 => 'Excellent - Service exceptionnel'
        ];

        return $descriptions[$rating] ?? 'Note inconnue';
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;
        return $this;
    }

    public function getDateAvis(): ?\DateTimeInterface
    {
        return $this->dateAvis;
    }

    public function setDateAvis(\DateTimeInterface $dateAvis): static
    {
        $this->dateAvis = $dateAvis;
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
}

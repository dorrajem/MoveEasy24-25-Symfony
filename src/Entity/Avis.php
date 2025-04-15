<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
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
    #[Assert\LessThanOrEqual(
        value: "today",
        message: "La date ne peut pas être dans le futur"
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
    
    // Dans un contrôleur ou un service
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
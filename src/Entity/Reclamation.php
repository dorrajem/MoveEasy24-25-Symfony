<?php

namespace App\Entity;

use App\Repository\ReclamationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Service\AIService;

#[ORM\Entity(repositoryClass: ReclamationRepository::class)]
class Reclamation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "La description ne doit pas être vide.")]
    #[Assert\Length(
        min: 10,
        minMessage: "La description doit contenir au moins {{ limit }} caractères."
    )]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[Assert\NotBlank(message: "Veuillez choisir une catégorie.")]
    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[Assert\NotNull]
    #[Assert\Type(\DateTimeInterface::class)]
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date_soumission;

    #[Assert\Choice(choices: ['en attente', 'en cours', 'traitée'], message: "Statut invalide.")]
    #[ORM\Column(length: 255)]
    private ?string $statut = 'en attente'; // Default value

    #[Assert\NotNull(message: "L'utilisateur est requis.")]
    #[ORM\Column(type: 'integer')]
    private ?int $id_utilisateur = null;

    #[Assert\Length(
        max: 1000,
        maxMessage: "La réponse ne peut pas dépasser {{ limit }} caractères."
    )]
    #[ORM\Column(length: 1000, nullable: true)]
    private ?string $reponse = null;

    public function __construct()
    {
        $this->date_soumission = new \DateTime(); // set current date by default
    }
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
private ?string $predictedCategory = null;

#[ORM\Column(type: 'string', length: 255, nullable: true)]
private ?string $sentiment = null;
#[ORM\Column(length: 20,nullable: true)]
private ?string $telephone = null;

public function getTelephone(): ?string
{
    return $this->telephone;
}

public function setTelephone(string $telephone): self
{
    $this->telephone = $telephone;
    return $this;
}

// + GETTERS & SETTERS

public function getPredictedCategory(): ?string
{
    return $this->predictedCategory;
}

public function setPredictedCategory(?string $predictedCategory): self
{
    $this->predictedCategory = $predictedCategory;
    return $this;
}

public function getSentiment(): ?string
{
    return $this->sentiment;
}

public function setSentiment(?string $sentiment): self
{
    $this->sentiment = $sentiment;
    return $this;
}

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
    public function new(Request $request, EntityManagerInterface $entityManager, AIService $aiService): Response
{
    $reclamation = new Reclamation();
    $form = $this->createForm(ReclamationType::class, $reclamation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Analyser le texte avec l'IA
        $result = $aiService->analyzeReclamation($reclamation->getDescription());

        $reclamation->setSentiment($result['sentiment']);
        $reclamation->setPredictedCategory($result['predictedCategory']);

        $entityManager->persist($reclamation);
        $entityManager->flush();

        return $this->redirectToRoute('reclamation_index');
    }

    return $this->render('reclamation/new.html.twig', [
        'form' => $form->createView(),
    ]);
}
}

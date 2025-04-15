<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontSecurityController extends AbstractController
{
    #[Route('/front/login', name: 'front_login', methods: ['GET', 'POST'])]
    public function login(): Response
    {
        // Votre firewall "front" interceptera la soumission du formulaire POST.
        // Vous affichez simplement la page de login.
        return $this->render('front/login.html.twig');
    }

    #[Route('/front/signup', name: 'front_signup', methods: ['GET', 'POST'])]
    public function signup(Request $request, EntityManagerInterface $entityManager): Response
    {
        $utilisateur = new Utilisateur();
        // Rôle par défaut pour un utilisateur qui s'inscrit côté front
        $utilisateur->setRole('Employé');

        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Stockage en clair pour l'exercice
            $plainPassword = $form->get('plainPassword')->getData();
            $utilisateur->setMotDePasse($plainPassword);

            $entityManager->persist($utilisateur);
            $entityManager->flush();

            // Une fois inscrit, on redirige vers la page de login front
            $this->addFlash('success', 'Inscription réussie ! Connectez-vous pour continuer.');
            return $this->redirectToRoute('front_login');
        }

        return $this->render('front/signup_front.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/front/logout', name: 'front_logout')]
    public function logout(): void
    {
        // Géré par le firewall -> aucune logique ici
        throw new \LogicException('This method can be blank - it will be intercepted by the logout mechanism.');
    }
}

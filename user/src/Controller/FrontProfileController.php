<?php

namespace App\Controller;

use App\Entity\ProfilUtilisateur;
use App\Form\ProfilUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/front/profile')]
class FrontProfileController extends AbstractController
{
    #[Route('/', name: 'front_profile', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur est bien connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('front_login');
        }

        // Récupérer le profil de l'utilisateur connecté
        $profil = $user->getProfil();
        if (!$profil) {
            $profil = new ProfilUtilisateur();
            $profil->setUtilisateur($user);
        }

        // Création du formulaire pour modifier le profil
        $form = $this->createForm(ProfilUtilisateurType::class, $profil);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($profil);
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour.');
            return $this->redirectToRoute('front_profile');
        }

        return $this->render('front/profile.html.twig', [
            'form' => $form->createView(),
            'profil' => $profil,
        ]);
    }
}

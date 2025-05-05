<?php
// src/Controller/FrontProfileController.php

namespace App\Controller;

use App\Entity\ProfilUtilisateur;
use App\Form\ProfilUtilisateurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontProfileController extends AbstractController
{
    #[Route('/front/profile', name: 'front_profile', methods: ['GET','POST'])]
    public function profile(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // 1️⃣ Récupère l'utilisateur et son profil
        $user   = $this->getUser();
        $profil = $user->getProfil();

        // Si pas de profil existant, on en crée un et on l'attache
        if (null === $profil) {
            $profil = new ProfilUtilisateur();
            $user->setProfil($profil);
            $em->persist($profil);
        }

        // 2️⃣ Formulaire
        $form = $this->createForm(ProfilUtilisateurType::class, $profil);
        $form->handleRequest($request);

        // 3️⃣ Soumission valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            // —> évite la sérialisation de File dans la session
            $profil->setPhotoProfilFile(null);

            $this->addFlash('success', 'Votre profil a bien été mis à jour.');
            return $this->redirectToRoute('front_profile');
        }

        return $this->render('front/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

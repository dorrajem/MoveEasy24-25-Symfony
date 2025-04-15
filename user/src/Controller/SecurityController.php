<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; // notez bien "Annotation" ou "Attribute" selon votre version
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirection si l'utilisateur est déjà connecté (optionnel)
        if ($this->getUser()) {
            // Redirigez vers une page par défaut (modifiez 'target_route' avec votre route cible)
            return $this->redirectToRoute('target_route');
        }

        // Récupère l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    // Modification de la route de logout pour qu'elle corresponde au pattern du firewall
    #[Route(path: '/utilisateur/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode ne doit jamais être exécutée car Symfony interceptera la requête
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

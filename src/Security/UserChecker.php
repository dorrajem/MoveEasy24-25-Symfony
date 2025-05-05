<?php
// src/Security/UserChecker.php
namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        // Ne concernera que vos Utilisateur
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Si pas encore vérifié, bloquer la connexion
        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'Vous devez confirmer votre adresse email avant de vous connecter.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien à faire après authentification
    }
}

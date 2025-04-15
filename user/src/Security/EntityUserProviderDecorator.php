<?php

namespace App\Security;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class EntityUserProviderDecorator extends EntityUserProvider
{
    /**
     * @param ManagerRegistry $registry
     * @param string          $class    La classe de l'entité, par exemple "App\Entity\Utilisateur"
     * @param string|null     $property Le nom de la propriété à utiliser pour l'identifiant (par défaut "email")
     */
    public function __construct(ManagerRegistry $registry, string $class, ?string $property = 'email')
    {
        parent::__construct($registry, $class, $property);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = parent::loadUserByIdentifier($identifier);

        // Si l'utilisateur est un proxy, forcez son initialisation
        if ($user instanceof \Doctrine\Persistence\Proxy) {
            $user->__load();
            // Hack: forcer la conversion en une instance concrète via la sérialisation
            $user = unserialize(serialize($user));
        }

        if (!$user instanceof UserInterface) {
            throw new \LogicException(sprintf('User with identifier "%s" does not implement UserInterface.', $identifier));
        }
        return $user;
    }
}

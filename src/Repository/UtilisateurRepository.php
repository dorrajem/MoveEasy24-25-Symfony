<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    /**
     * Retourne les Utilisateur dont nom, prénom ou email contient $term.
     * Si $term est vide, renvoie tout.
     *
     * @param string|null $term
     * @return Utilisateur[]
     */
    public function findBySearch(?string $term): array
    {
        $qb = $this->createQueryBuilder('u');

        if ($term) {
            $qb->andWhere('u.nom    LIKE :t OR
                           u.prenom LIKE :t OR
                           u.email  LIKE :t')
               ->setParameter('t', '%'.$term.'%');
        }

        return $qb
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}


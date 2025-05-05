<?php

namespace App\Repository;

use App\Entity\AvisReaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AvisReaction>
 */
class AvisReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AvisReaction::class);
    }

    public function findOneByUserAndAvis(int $userId, int $avisId): ?AvisReaction
    {
        return $this->createQueryBuilder('r')
            ->where('r.utilisateurId = :userId')
            ->andWhere('r.avis = :avisId')
            ->setParameter('userId', $userId)
            ->setParameter('avisId', $avisId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
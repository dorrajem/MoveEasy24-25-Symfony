<?php

// src/Repository/EquipementRepository.php
// src/Repository/EquipementRepository.php
namespace App\Repository;

use App\Data\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Equipement;

/**
 * @extends ServiceEntityRepository<Equipement>
 */
class EquipementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipement::class);
    }

    /**
     * @param SearchData $search
     * @return Equipement[]
     */
    public function findBySearch(SearchData $search): array
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($search->type)) {
            $qb->andWhere('LOWER(e.typeEquipement) LIKE :type')
               ->setParameter('type', strtolower($search->type) . '%');
        }

        if (!empty($search->statut)) {
            $qb->andWhere('e.statut = :statut')
               ->setParameter('statut', $search->statut);
        }

        // Tri alphabétique croissant
        $qb->orderBy('e.typeEquipement', 'ASC');

        return $qb->getQuery()->getResult();
    }
}



    //    /**
    //     * @return Equipement[] Returns an array of Equipement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Equipement
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }


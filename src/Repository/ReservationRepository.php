<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Count the total number of seats reserved for a specific train on a date and time
     */
    public function countReservedSeats(int $trainId, \DateTimeInterface $date, \DateTimeInterface $time): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('SUM(r.nbrplace)')
            ->where('r.idTrain = :trainId')
            ->andWhere('r.dateR = :date')
            ->andWhere('r.heureR = :time')
            ->setParameter('trainId', $trainId)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('time', $time->format('H:i:s'));
            
        $result = $qb->getQuery()->getSingleScalarResult();
        
        return $result ? (int)$result : 0;
    }
}
//    /**
//     * @return Reservation[] Returns an array of Reservation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Reservation
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


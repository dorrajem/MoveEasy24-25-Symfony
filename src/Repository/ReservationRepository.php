<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    /**
     * Search reservations by a keyword across multiple fields.
     *
     * @param string $keyword
     * @return Reservation[]
     */
    public function searchByKeyword(string $keyword): array
    {
        $keyword = trim(strtolower($keyword));

        $qb = $this->createQueryBuilder('r');

        if (!empty($keyword)) {
            $qb->where('LOWER(r.destinationR) LIKE :kw')
                ->orWhere('LOWER(r.typeTrainR) LIKE :kw')
                ->orWhere('LOWER(r.typeplace) LIKE :kw')
                ->orWhere('LOWER(r.etatR) LIKE :kw')
                ->orWhere('LOWER(r.NameUser) LIKE :kw')
                ->setParameter('kw', '%' . $keyword . '%');
        }

        return $qb->orderBy('r.dateR', 'DESC')
                 ->getQuery()
                 ->getResult();
    }

    /**
     * Count the total number of seats reserved for a specific train on a date and time.
     *
     * @param int $trainId
     * @param \DateTimeInterface $date
     * @param \DateTimeInterface $time
     * @return int
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

        return $result ? (int) $result : 0;
    }
}

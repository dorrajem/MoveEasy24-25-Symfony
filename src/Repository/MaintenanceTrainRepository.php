<?php

namespace App\Repository;

use App\Entity\MaintenanceTrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaintenanceTrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MaintenanceTrain::class);
    }

    public function findByTrain($idTrain)
    {
        return $this->createQueryBuilder('m')
            ->join('m.train', 't')
            ->where('t.idTrain = :idTrain')
            ->setParameter('idTrain', $idTrain)
            ->orderBy('m.dateMaintenance', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
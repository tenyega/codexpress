<?php

namespace App\Repository;

use App\Entity\View;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<View>
 */
class ViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, View::class);
    }

    /**
     * @return View[] Returns an array of View objects
     */
    public function findBynote($note_id): array
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.note = :note_id')
            ->setParameter('note_id', $note_id)
            ->getQuery()
            ->getResult()
        ;
    }

    //    public function findOneBySomeField($value): ?View
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

<?php

namespace App\Repository;

use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Note>
 */
class NoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Note::class);
    }

    /**
     * @return array
     * this method is used for the search bar which awe have integrated as a html 
     */
    public function findByQuery($query): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.is_public = true')
            ->andWhere('(n.title LIKE :q OR n.content LIKE :q)')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('n.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    //Getting the 3 latest note of a particular user by its id and public note with descending order 
    public function findByCreator($id): array
    {
        return $this->createQueryBuilder('n')
            ->where('n.is_public= true')
            ->andWhere('n.creator = :id')
            ->setParameter('id', $id)
            ->orderBy('n.created_at', 'DESC')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;
    }
}

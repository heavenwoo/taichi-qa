<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return Query
     */
    public function findLatestQuery(): Query
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT q, u, a, t
                FROM App:Question q
                JOIN q.user u
                LEFT JOIN q.answers a
                LEFT JOIN q.tags t
                WHERE q.createdAt <= :now
                ORDER BY q.createdAt DESC
            ')
            ->setParameter('now', new \DateTime());
    }

    public function findHottestQuery(): Query
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT q, u, a, t
                FROM App:Question q
                JOIN q.user u
                LEFT JOIN q.answers a
                LEFT JOIN q.tags t
                WHERE q.createdAt <= :now
                ORDER BY q.views DESC, q.createdAt DESC
            ')
            ->setParameter('now', new \DateTime());
    }

    /**
     * @return Query
     */
    public function getListQuery(): Query
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT q, u, a, t
                FROM App:Question q
                JOIN q.user u
                LEFT JOIN q.answers a
                LEFT JOIN q.tags t
                ORDER BY q.createdAt DESC
            ');
    }

    /**
     * public function findBySomething($value)
     * {
     * return $this->createQueryBuilder('q')
     * ->where('q.something = :value')->setParameter('value', $value)
     * ->orderBy('q.id', 'ASC')
     * ->setMaxResults(10)
     * ->getQuery()
     * ->getResult()
     * ;
     * }
     */
}

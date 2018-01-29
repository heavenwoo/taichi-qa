<?php

namespace Vega\Repository;

use Vega\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPostListQuery()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 'c', 't', 'u')
            ->join('p.user', 'u')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('p.tags', 't')
            ->where('p.createdAt <= :now')->setParameter('now', new \DateTime())
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }

    public function getPostById(int $id)
    {
        $query = $this->createQueryBuilder('p')
            ->select('p', 'c', 't', 'u')
            ->join('p.user', 'u')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('p.tags', 't')
            ->where('p.id = :id')->setParameter('id', $id)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('p')
            ->where('p.something = :value')->setParameter('value', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}

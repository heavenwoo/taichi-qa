<?php

namespace Vega\Repository;

use Vega\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vega\Entity\Tag;

class PostRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findPostsListQuery()
    {
        return $this->createQueryBuilder('p')
            ->select('p', 't', 'u')
            ->join('p.user', 'u')
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

    public function findPostsQueryByTag(Tag $tag)
    {
        return $this->createQueryBuilder('p')
            ->select('p', 't', 'u')
            ->join('p.user', 'u')
            ->leftJoin('p.tags', 't')
            ->where('t = :tag')->setParameter('tag', $tag)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery();
    }
}

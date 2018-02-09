<?php

namespace Vega\Repository;

use Doctrine\ORM\QueryBuilder;
use Vega\Entity\Comment;
use Vega\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vega\Entity\Tag;
use Vega\Entity\User;

class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @return QueryBuilder
     */
    private function getQuestionQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->select('q', 'u', 'a', 't')
            ->join('q.user', 'u')
            ->leftJoin('q.answers', 'a')
            ->leftJoin('q.tags', 't')
            ->where('q.createdAt <= :now')->setParameter('now', new \DateTime());
    }

    public function findLatestQuery(): Query
    {
        return $this->getQuestionQueryBuilder()
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery();
    }

    public function findHottestQuery(): Query
    {
        return $this->getQuestionQueryBuilder()
            ->orderBy('q.views', 'DESC')
            ->addOrderBy('q.createdAt', 'DESC')
            ->getQuery();
    }

    public function findUnansweredQuery(): Query
    {
        return $this->getQuestionQueryBuilder()
            ->andWhere('q.answerNums = 0')
            ->orderBy('q.views', 'DESC')
            ->addOrderBy('q.createdAt', 'DESC')
            ->getQuery();
    }

    /**
     * @return Query
     */
    public function getListQuery(): Query
    {
        return $this->getQuestionQueryBuilder()
            ->getQuery();
    }

    public function getQuestionById(int $id)
    {
        $query = $this->createQueryBuilder('q')
            ->select('q', 'u', 'c', 't')
            ->join('q.user', 'u')
            ->leftJoin('q.comments', 'c')
            ->leftJoin('q.tags', 't')
            ->where('q.id = :id')->setParameter('id', $id)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findQuestionsQueryByTag(Tag $tag)
    {
        return $this->getQuestionQueryBuilder()
            ->andWhere('t = :tag')->setParameter('tag', $tag)
            ->orderBy('q.createdAt', 'DESC')
            ->getQuery();
    }

    public function findQuestionsByUser(User $user)
    {
        return $this->createQueryBuilder('q')
            ->where('q.user = :user')->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}

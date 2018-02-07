<?php

namespace Vega\Repository;

use Doctrine\ORM\Query;
use Vega\Entity\Answer;
use Vega\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Vega\Entity\User;

class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findAllAnswersQueryByQuestion(Question $question): Query
    {
        return $this->createQueryBuilder('a')
            ->select('a', 'u', 'c')
            ->join('a.user', 'u')
            ->leftJoin('a.comments', 'c')
            ->where('a.question = :question')->setParameter('question', $question)
            ->orderBy('a.best', 'DESC')
            ->addOrderBy('a.createdAt', 'DESC')
            ->getQuery();
    }

    public function findAnswersByUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}

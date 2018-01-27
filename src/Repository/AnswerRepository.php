<?php

namespace Taichi\Repository;

use Doctrine\ORM\Query;
use Taichi\Entity\Answer;
use Taichi\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

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
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery();
    }
}

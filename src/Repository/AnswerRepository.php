<?php

namespace App\Repository;

use App\Entity\Answer;
use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public function findAllAnswersQueryByQuestion(Question $question)
    {
        return $this->getEntityManager()
            ->createQuery('
                SELECT a, u, c
                FROM App:Answer a
                JOIN a.user u 
                LEFT JOIN a.comments c 
                WHERE a.question = :question
                ORDER BY a.createdAt DESC 
            ')
            ->setParameter('question', $question);
    }

    /*
    public function findBySomething($value)
    {
        return $this->createQueryBuilder('a')
            ->where('a.something = :value')->setParameter('value', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
}

<?php

namespace Taichi\Controller;

use Taichi\Entity\Question;
use Taichi\Repository\{
    AnswerRepository, QuestionRepository, TagRepository
};
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{
    Route,
    Cache,
    Method,
    Template
};
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

/**
 * Class QuestionController
 *
 * @Cache(maxage="1", public=true)
 * @package Taichi\Controller
 */
class QuestionController extends Controller
{
    /**
     * @Route("/list", name="question_list")
     * @Method("GET")
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     */
    public function list(QuestionRepository $questionRepository, Request $request)
    {
        //
    }

    /**
     * @Route("/question/{id}", name="question_show", requirements={"id": "\d+"})
     * @Method("GET")
     * @Template()
     *
     * @param int $id
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     * @param TagRepository $tagRepository
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function show(int $id, QuestionRepository $questionRepository, AnswerRepository $answerRepository, TagRepository $tagRepository, Request $request)
    {
        $settings = $this->getSettings();

        $question = $questionRepository->getQuestionById($id);

        if ($question == null) {
            return $this->redirectToRoute('question_index');
        }

        $paginator = $this->get('knp_paginator');

        $answers = $paginator->paginate(
            $answerRepository->findAllAnswersQueryByQuestion($question),
            $request->query->getInt('page', 1),
            10
        );

        return [
            'question' => $question,
            'answers' => $answers,
            'setting' => $settings,
            'tags' => $tagRepository->findBy([], null, 10),
        ];
    }

    /**
     * @Route("/question/create", name="question_create")
     * @Method({"GET", "POST"})
     *
     */
    public function create()
    {
        //
    }

    /**
     * @Route("/question/edit/{id}", name="question_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Question $question
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * @Route("/question/edit/{id}", name="question_edit", requirements={"id": "\d+"})
     * @Method("POST")
     *
     * @param Question $question
     */
    public function delete(Question $question)
    {
        //
    }
}

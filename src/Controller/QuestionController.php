<?php

namespace Vega\Controller;

use Vega\Entity\Answer;
use Vega\Entity\Comment;
use Vega\Entity\Question;
use Vega\Form\AnswerType;
use Vega\Form\CommentType;
use Vega\Form\QuestionType;
use Vega\Repository\{
    AnswerRepository, QuestionRepository, TagRepository, UserRepository
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
 * @Route("/question")
 * @package Vega\Controller
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
     * @Route("/{id}", name="question_show", requirements={"id": "\d+"})
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

        if (null == $question) {
            return $this->redirectToRoute('question_index');
        }

        $paginator = $this->get('knp_paginator');

        $answers = $paginator->paginate(
            $answerRepository->findAllAnswersQueryByQuestion($question),
            $request->query->getInt('page', 1),
            20
        );

        $answer = new Answer();
        $comment = new Comment();
        $answerForm = $this->createForm(AnswerType::class, $answer);
        $commentForm = $this->createForm(CommentType::class, $comment);

        return [
            'question' => $question,
            'answers' => $answers,
            'setting' => $settings,
            'tags' => $tagRepository->findBy([], null, 10),
            'answerForm' => $answerForm->createView(),
            'commentForm' => $commentForm->createView(),
        ];
    }

    /**
     * @Route("/create", name="question_create")
     * @Method({"GET", "POST"})
     * @Template()
     *
     */
    public function create(Request $request, UserRepository $userRepository)
    {
        $question = new Question();
        $question->setUser($userRepository->findOneBy(['username' => 'heaven']));
        $form = $this->createForm(QuestionType::class, $question);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setCreatedAt(new \DateTime());
            $question->setUpdatedAt($question->getCreatedAt());

            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            $this->addFlash('success', 'Question created successfully!');

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return [
            'question' => $question,
            'form' => $form->createView()
        ];
    }

    /**
     * @Route("/edit/{id}", name="question_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Question $question
     */
    public function edit(Request $request, Question $question)
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $question->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Question updated successfully!');

            return $this->redirectToRoute('question_show', ['id' => $question->getId()]);
        }

        return [
            'question' => $question,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/{id}/delete", name="question_delete", requirements={"id": "\d+"})
     * @Method("POST")
     *
     * @param Question $question
     */
    public function delete(Request $request, Question $question)
    {
        $question->getTags()->clear();
        $question->getAnswers()->clear();
        $question->getComments()->clear();

        $em = $this->getDoctrine()->getManager();
        $em->remove($question);
        $em->flush();

        $this->addFlash('success', 'Question deleted successfully!');

        return $this->redirectToRoute('question_list');
    }
}

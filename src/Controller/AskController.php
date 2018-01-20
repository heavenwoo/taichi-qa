<?php

namespace App\Controller;

use App\Repository\QuestionRepository;
use App\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

/**
 * Class AskController
 *
 * @Cache(maxage="1", public=true)
 * @package App\Controller
 */
class AskController extends Controller
{
    /**
     * @Route("/", name="ask_index", options={"sitemap"=true})
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, QuestionRepository $questionRepository, TagRepository $tagRepository): Response
    {
        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $questionRepository->findLatestQuery(),
            $request->query->getInt('page', 1),
            10
        );

        $tags = $tagRepository->findBy([], null, 10);

        return $this->render("ask/index.html.twig", [
            'questions' => $questions,
            'tags' => $tags,
        ]);
    }

    /**
     * @Route("/list", name="ask_list")
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     */
    public function list(QuestionRepository $questionRepository, Request $request)
    {
        //
    }
}

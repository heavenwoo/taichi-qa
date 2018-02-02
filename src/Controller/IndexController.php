<?php

namespace Vega\Controller;

use Vega\Repository\QuestionRepository;
use Vega\Repository\TagRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends Controller
{
    /**
     * @Route("/", name="question_index", options={"sitemap"=true})
     * @Method("GET")
     *
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @param TagRepository $tagRepository
     * @return Response
     */
    public function index(Request $request, QuestionRepository $questionRepository, TagRepository $tagRepository): Response
    {
        $settings = $this->getSettings();
        $index = $this->getParameter('index');

        $sort = $request->query->get('sort', null);

        if ($sort == 'hottest') {
            $query = $questionRepository->findHottestQuery();
        } elseif ($sort == 'latest' || $sort == '') {
            $query = $questionRepository->findLatestQuery();
        } elseif ($sort == 'unanswered') {
            $query = $questionRepository->findUnansweredQuery();
        }

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $index['tag_nums']);

        return $this->render("question/index.html.twig", [
            'questions' => $questions,
            'tags' => $tags,
            'setting' => $settings,
            'sort' => $sort,
        ]);
    }
}

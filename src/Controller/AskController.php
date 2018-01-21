<?php

namespace App\Controller;

use App\Entity\Setting;
use App\Repository\{
    QuestionRepository,
    TagRepository
};
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{
    Route,
    Cache,
    Template
};
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};

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
     * @Template()
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, QuestionRepository $questionRepository, TagRepository $tagRepository): array
    {
        $settings = $this->getSettings();

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $questionRepository->findLatestQuery(),
            $request->query->getInt('page', 1),
            $settings['index_question_nums']
        );

        $tags = $tagRepository->findBy([], null, $settings['index_tag_nums']);

        return [
            'questions' => $questions,
            'tags' => $tags,
            'setting' => $settings,
        ];
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

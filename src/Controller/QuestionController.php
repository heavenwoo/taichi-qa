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
 * Class QuestionController
 *
 * @Cache(maxage="1", public=true)
 * @package App\Controller
 */
class QuestionController extends Controller
{
    /**
     * @Route("/", name="question_index", options={"sitemap"=true})
     * @Template()
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, QuestionRepository $questionRepository, TagRepository $tagRepository): array
    {
        $settings = $this->getSettings();

        $sort = $request->query->get('sort', null);
        $page = $request->query->getInt('page', 1);

        if ($sort == 'hottest') {
            $query = $questionRepository->findHottestQuery();
        } elseif ($sort == 'newest' || $sort == '') {
            $query = $questionRepository->findLatestQuery();
        }

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $query,
            $page,
            $settings['index_question_nums']
        );

        $tags = $tagRepository->findBy([], null, $settings['index_tag_nums']);

        return [
            'questions' => $questions,
            'tags' => $tags,
            'setting' => $settings,
            'sort' => $sort,
        ];
    }

    /**
     * Route("/hottest", name="ask_hottest_index")
     *
     * @param Request $request
     * @param QuestionRepository $questionRepository
     * @return Response
     */
    public function hottest(Request $request, QuestionRepository $questionRepository, TagRepository $tagRepository): Response
    {
        $settings = $this->getSettings();

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $questionRepository->findHottestQuery(),
            $request->query->getInt('page', 1),
            $settings['index_question_nums']
        );

        return $this->render('ask/index.html.twig', [
            'questions' => $questions,
            'tags' => $tagRepository->findBy([], null, $settings['index_tag_nums']),
            'setting' => $settings,
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

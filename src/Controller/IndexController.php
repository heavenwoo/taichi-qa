<?php
/**
 * Created by PhpStorm.
 * User: heave
 * Date: 1/23/2018
 * Time: 00:19
 */

namespace App\Controller;

use App\Entity\Question;
use App\Repository\{
    AnswerRepository, QuestionRepository, TagRepository
};
use Sensio\Bundle\FrameworkExtraBundle\Configuration\{
    Route,
    Cache,
    Method
};
use Symfony\Component\HttpFoundation\{
    Request,
    Response
};


class IndexController extends Controller
{
    /**
     * @Route("/", name="question_index", options={"sitemap"=true})
     * @Method("GET")
     *
     * @param QuestionRepository $questionRepository
     * @param Request $request
     * @return Response
     */
    public function index(Request $request, QuestionRepository $questionRepository, TagRepository $tagRepository): Response
    {
        $settings = $this->getSettings();

        //$this->getParameter('taichi_qa');

        $sort = $request->query->get('sort', null);

        if ($sort == 'hottest') {
            $query = $questionRepository->findHottestQuery();
        } elseif ($sort == 'newest' || $sort == '') {
            $query = $questionRepository->findLatestQuery();
        }

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $settings['index_question_nums']
        );

        $tags = $tagRepository->findBy([], null, $settings['index_tag_nums']);

        return $this->render("question/index.html.twig", [
            'questions' => $questions,
            'tags' => $tags,
            'setting' => $settings,
            'sort' => $sort,
        ]);
    }
}
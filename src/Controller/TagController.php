<?php

namespace Vega\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vega\Repository\PostRepository;
use Vega\Repository\QuestionRepository;
use Vega\Repository\TagRepository;
use Vega\Entity\Tag;

/**
 * Class TagController
 *
 * @Route("/tag")
 *
 * @package Vega\Controller
 */
class TagController extends Controller
{
    /**
     * @Route("/", name="tag_index")
     */
    public function index()
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
    }

    /**
     * @Route("/{name}", name="tag_list", requirements={"name": "\w+"})
     *
     * @param Request $request
     * @param int $id
     * @param TagRepository $tagRepository
     */
    public function list(Request $request, Tag $tag, QuestionRepository $questionRepository, TagRepository $tagRepository): Response
    {
        $settings = $this->getSettings();
        $index = $this->getParameter('index');

        $query = $questionRepository->findQuestionsByTag($tag);

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $index['tag_nums']);

        return $this->render("tag/list.html.twig", [
            'questions' => $questions,
            'tags' => $tags,
            'setting' => $settings,
        ]);
    }

    /**
     * @Route("/{name}/post", name="tag_post_list", requirements={"name": "\w+"})
     *
     * @param Request $request
     * @param Tag $tag
     * @param PostRepository $postRepository
     * @param TagRepository $tagRepository
     */
    public function posts(Request $request, Tag $tag, PostRepository $postRepository, TagRepository $tagRepository)
    {
        $settings = $this->getSettings();
        $index = $this->getParameter('index');

        $query = $questionRepository->findQuestionsByTag($tag);

        $paginator = $this->get('knp_paginator');

        $questions = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $index['tag_nums']);

        return $this->render("tag/list.html.twig", [
            'questions' => $questions,
            'tags' => $tags,
            'setting' => $settings,
        ]);
    }
}

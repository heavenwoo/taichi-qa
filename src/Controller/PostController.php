<?php

namespace Vega\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\{
    Route,
    Template
};
use Symfony\Component\HttpFoundation\{
    Response,
    Request
};
use Vega\Repository\{
    PostRepository,
    TagRepository
};

/**
 * Class PostController
 *
 * @Route("/post")
 *
 * @package Vega\Controller
 */
class PostController extends Controller
{
    /**
     * @Route("", name="post_list")
     * @Template()
     */
    public function list(Request $request, PostRepository $postRepository, TagRepository $tagRepository)
    {
        $settings = $this->getSettings();
        $index = $this->getParameter('index');

        $paginator = $this->get('knp_paginator');

        $posts = $paginator->paginate(
            $postRepository->findPostListQuery(),
            $request->query->getInt('page', 1),
            20
        );

        $tags = $tagRepository->findBy([], null, $index['tag_nums']);

        return [
            'posts' => $posts,
            'tags' => $tags,
            'setting' => $settings,
        ];
    }

    /**
     * @Route("/show/{id}", name="post_show")
     */
    public function show()
    {

    }
}

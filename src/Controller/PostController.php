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

class PostController extends Controller
{
    /**
     * @Route("/post", name="post_list")
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
            10
        );

        $tags = $tagRepository->findBy([], null, $index['tag_nums']);

        return [
            'posts' => $posts,
            'tags' => $tags,
            'setting' => $settings,
        ];
    }
}

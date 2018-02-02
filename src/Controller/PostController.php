<?php

namespace Vega\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Vega\Repository\PostRepository;
use Vega\Repository\TagRepository;

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
     */
    public function list(Request $request, PostRepository $postRepository, TagRepository $tagRepository): Response
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

        return $this->render("post/list.html.twig", [
            'posts' => $posts,
            'tags' => $tags,
            'setting' => $settings,
        ]);
    }

    /**
     * @Route("/show/{id}", name="post_show")
     */
    public function show()
    {
    }
}

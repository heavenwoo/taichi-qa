<?php

namespace Vega\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\{
    Route,
    Template
};
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class UserController
 *
 * @Route("/user")
 *
 * @package Vega\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user")
     */
    public function index()
    {
        // replace this line with your own code!
        return $this->render('@Maker/demoPage.html.twig', [ 'path' => str_replace($this->getParameter('kernel.project_dir').'/', '', __FILE__) ]);
    }

    /**
     * @Route("/login", name="user_login")
     * @Template()
     *
     * @param AuthenticationUtils $helper
     * @return array
     */
    public function login(AuthenticationUtils $helper)
    {
        $setting = $this->getSettings();

        return [
            'setting' => $setting,
            'last_username' => $helper->getLastUsername(),
            'error' => $helper->getLastAuthenticationError(),
        ];
    }

    /**
     * @Route("/logout", name="user_logout")
     *
     * @return array
     */
    public function logout()
    {
        return [];
    }
}

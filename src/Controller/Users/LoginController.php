<?php

namespace App\Controller\Users;

use Twig\Environment;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class LoginController
{
    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        Environment $twig
    )
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="trick_login")
     */
    public function login()
    {

        return new Response($this->twig->render(
            'users/login.html.twig', [
        ]));
    }
}
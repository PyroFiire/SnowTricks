<?php

namespace App\Controller\Users;

use Twig\Environment;

use App\Form\LoginType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController
{

    /**
     * @var Environment
     */
    private $twig;
    
    /**
     * @var FormFactoryInterface
     */
    private $form;

    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;
    
    
    public function __construct(
        Environment $twig,
        FormFactoryInterface $form,
        AuthenticationUtils $authenticationUtils
    )
    {
        $this->twig = $twig;
        $this->form = $form;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/login", name="user_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $formLogin = $this->form->create(LoginType::class);

        return new Response($this->twig->render(
            'users/login.html.twig', [
            'formLogin' => $formLogin->createView(),
            'last_username' => $lastUsername,
            'error'         => $error,
        ]));
    }
}

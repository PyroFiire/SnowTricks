<?php

namespace App\Controller\Users;

use App\Entity\User;
use App\Form\RegistrationType;

use Twig\Environment;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RegistrationController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var Environment
     */
    private $twig;
    
    /**
     * @var FormFactoryInterface
     */
    private $form;
    
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        EntityManagerInterface $manager
    )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->form = $form;
        $this->manager = $manager;
    }

    /**
     * @Route("/registration", name="user_registration")
     */
    public function registration(Request $request)
    {
        $formRegistration = $this->form->create(RegistrationType::class, $user = new User());

        $formRegistration->handleRequest($request);

        if($formRegistration->isSubmitted() && $formRegistration->isValid()){
 
            //set
            //$user->setPassword();
            //$user->setActiveToken
            $user->setActive(false);
            
            $user->setPicturePath('default_avatar.jpg');
            $this->manager->persist($user);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'user_registration',
                []
            ));
        }

        return new Response($this->twig->render(
            'users/registration.html.twig', [
            'user' => $user,
            'formRegistration' => $formRegistration->createView(),
        ]));
    }
}

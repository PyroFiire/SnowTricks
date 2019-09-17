<?php

namespace App\Controller\Users;

use App\Entity\User;
use Twig\Environment;

use App\Form\RegistrationType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Security\TokenSecurity;


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
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var TokenSecurity
     */
    private $tokenSecurity;
    
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $manager,
        TokenSecurity $tokenSecurity
    )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->form = $form;
        $this->passwordEncoder = $passwordEncoder;
        $this->manager = $manager;
        $this->tokenSecurity = $tokenSecurity;
    }

    /**
     * @Route("/registration", name="user_registration")
     */
    public function registration(Request $request, MailerInterface $mailer)
    {   
        $formRegistration = $this->form->create(RegistrationType::class, $user = new User());

        $formRegistration->handleRequest($request);

        if($formRegistration->isSubmitted() && $formRegistration->isValid()){
 
            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setActive(false);
            $user->setActiveToken($this->tokenSecurity->generateToken());
            $user->setPicturePath('default_avatar.jpg');

            $email = (new TemplatedEmail())
                ->from('snowtrick11@gmail.com')
                ->to($user->getEmail())
                ->subject('Active your account !')
                ->htmlTemplate('emails/registration.html.twig')
                ->context([
                    'username' => $user->getUsername(),
                    'activeToken' => $user->getActiveToken()
                ])
            ;
            
            $mailer->send($email);
            $this->manager->persist($user);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'index'
            ));
        }

        return new Response($this->twig->render(
            'users/registration.html.twig', [
            'user' => $user,
            'formRegistration' => $formRegistration->createView(),
        ]));
    }

}

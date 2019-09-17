<?php

namespace App\Controller\Users;

use Twig\Environment;

use App\Form\ForgotPasswordType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Security\TokenSecurity;
use App\Repository\UserRepository;

class ForgotPasswordController
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
     * @var TokenSecurity
     */
    private $tokenSecurity;
    
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        EntityManagerInterface $manager,
        TokenSecurity $tokenSecurity,
        UserRepository $userRepository
    )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->form = $form;
        $this->manager = $manager;
        $this->tokenSecurity = $tokenSecurity;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/forgotPassword", name="user_forgotPassword")
     */
    public function ForgotPassword(Request $request, MailerInterface $mailer)
    {   
        $formForgotPassword = $this->form->create(ForgotPasswordType::class);

        $formForgotPassword->handleRequest($request);

        if($formForgotPassword->isSubmitted() && $formForgotPassword->isValid()){

            $user = $this->userRepository->findOneByUsername($request->request->get('forgot_password'));
            if(!$user){
                $user = $this->userRepository->findOneByEmail($request->request->get('forgot_password'));
            }
            if(!$user){
                throw new \Exception('username or email not found');
            }

            $user->setForgotPasswordToken($this->tokenSecurity->generateToken());

            $email = (new TemplatedEmail())
                ->from('christophe.guinot@hotmail.fr')
                ->to('christophe.guinot@hotmail.fr')
                ->subject('Forgot Password')
                ->htmlTemplate('emails/forgotPassword.html.twig')
                ->context([
                    'username' => $user->getUsername(),
                    'forgotPasswordToken' => $user->getForgotPasswordToken()
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
            'users/forgotPassword.html.twig', [
            'formForgotPassword' => $formForgotPassword->createView(),
        ]));
    }

}

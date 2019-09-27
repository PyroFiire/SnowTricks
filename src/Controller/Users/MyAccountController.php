<?php

namespace App\Controller\Users;

use Twig\Environment;

use App\Form\AvatarType;
use App\Form\ResetPasswordType;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Security\TokenSecurity;
use Symfony\Component\Filesystem\Filesystem;


class MyAccountController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var TokenSecurity
     */
    private $TokenSecurity;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var FormFactoryInterface
     */
    private $form;

    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(
        Environment $twig,
        FormFactoryInterface $form,
        Security $security,
        ContainerInterface $container,
        EntityManagerInterface $manager,
        UrlGeneratorInterface $router,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenSecurity $tokenSecurity,
        Filesystem $filesystem
    )
    {
        $this->twig = $twig;
        $this->form = $form;
        $this->security = $security;
        $this->container = $container;
        $this->manager = $manager;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenSecurity = $tokenSecurity;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/myAccount", name="user_myAccount")
     */
    public function myAccount(Request $request)
    {
        $user = $this->security->getUser();
        $lastPathAvatar = $user->getPicturePath();

        $formAvatar = $this->form->create(AvatarType::class, $user);
        $formResetPassword = $this->form->create(ResetPasswordType::class);

        $formAvatar->handleRequest($request);
        $formResetPassword->handleRequest($request);
        
        if($formAvatar->isSubmitted() && $formAvatar->isValid()){

            $newPicturePath = $formAvatar['picturePath']->getData();

                $newFileName = uniqid().'.'.$newPicturePath->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $this->filesystem->remove([$this->container->getParameter('avatars_directory').'/'.$lastPathAvatar]);
                    $newPicturePath->move($this->container->getParameter('avatars_directory'), $newFileName );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $user->setPicturePath($newFileName);
            


            $this->manager->persist($user);
            $this->manager->flush();

            /*return new RedirectResponse($this->router->generate(
                'user_myAccount'
            ));*/
        }

        if($formResetPassword->isSubmitted() && $formResetPassword->isValid()){

            $user = $this->security->getUser();
            $typedPassword = $request->request->get('reset_password')['password'];

            if(!$this->passwordEncoder->isPasswordValid($user, $typedPassword)){
                throw new \Exception('wrong password');
            }

            $user->setForgotPasswordToken($this->tokenSecurity->generateToken());
            $this->manager->persist($user);
            $this->manager->flush();
            
            return new RedirectResponse($this->router->generate(
                'user_resetPassword',[
                    'username' => $user->getUsername(),
                    'forgotPasswordToken' => $user->getForgotPasswordToken()
                ]
            ));

        }
        return new Response($this->twig->render(
            'users/myAccount.html.twig', [
            'formAvatar' => $formAvatar->createView(),
            'formResetPassword' => $formResetPassword->createView(),
        ]));
    }
}
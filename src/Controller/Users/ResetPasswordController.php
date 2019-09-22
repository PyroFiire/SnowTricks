<?php

namespace App\Controller\Users;

use Twig\Environment;

use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var FormFactoryInterface
     */
    private $form;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        Environment $twig,
        UserRepository $userRepository,
        UrlGeneratorInterface $router,
        EntityManagerInterface $manager,
        FormFactoryInterface $form,
        UserPasswordEncoderInterface $passwordEncoder

    )
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->manager = $manager;
        $this->form = $form;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/resetPassword/{username}/{forgotPasswordToken}", name="user_resetPassword")
     */
    public function resetPassword(Request $request)
    {
        $user = $this->userRepository->findOneByUsername($request->attributes->get('username'));

        // if token null || not egal
        if($user->getForgotPasswordToken()===null || $request->attributes->get('forgotPasswordToken') !== $user->getForgotPasswordToken()){
            throw new AccessDeniedHttpException('Token null or incorrect');
        }
        
        $formResetPassword = $this->form->create(ResetPasswordType::class, $user);
        $formResetPassword->handleRequest($request);
        if($formResetPassword->isSubmitted() && $formResetPassword->isValid()){

            $user->setPassword($this->passwordEncoder->encodePassword($user, $user->getPassword()));
            $user->setForgotPasswordToken(null);
            
            $this->manager->persist($user);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'index'
            ));
        }
        return new Response($this->twig->render(
            'users/resetPassword.html.twig', [
            'formResetPassword'=> $formResetPassword->createView()
        ]));
    }
}
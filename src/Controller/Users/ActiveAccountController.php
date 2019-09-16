<?php

namespace App\Controller\Users;

use Twig\Environment;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;

class ActiveAccountController
{
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

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
        EntityManagerInterface $manager
    )
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->manager = $manager;
    }

    /**
     * @Route("/activeAccount", name="user_activeAccount")
     */
    public function activeAccount(Request $request)
    {
        $user = $this->userRepository->findOneByUsername($request->query->get('username'));

        if($user->getActive()){
            throw new \Exception('this account is already active !');
        }
        elseif($request->query->get('activeToken') === $user->getActiveToken()){

            $user->setActive(true)
                 ->setActiveToken(null);

            $this->manager->persist($user);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'user_login'
            ));
        }
        else{
            throw new \Exception('Wrong Token !');
        }
    }
}
<?php

namespace App\Controller\Tricks;

use Twig\Environment;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Comment;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Form\CommentType;

class TrickShowController
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

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        EntityManagerInterface $manager,
        TrickRepository $trickRepository,
        CommentRepository $commentRepository
    )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->form = $form;
        $this->manager = $manager;
        $this->trickRepository = $trickRepository;
        $this->commentRepository = $commentRepository;
    }

    /**
     * @Route("/tricks/show/{slug}", name="trick_show")
     */
    public function trick_show(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));
        $comments = $this->commentRepository->findByTrick($trick);

        $formComment = $this->form->create(CommentType::class, $comment = new Comment());
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()){

            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            
            $this->manager->persist($comment);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'trick_show',
                ['slug' => $trick->getSlug()]
            ));
        }

        return new Response($this->twig->render(
            'tricks/trick.html.twig', [
            'trick' => $trick,
            'formComment' => $formComment->createView()
        ]));
    }
}
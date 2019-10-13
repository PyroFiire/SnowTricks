<?php

namespace App\Controller\Tricks\Ajax;

use Twig\Environment;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoadMoreComments
{
    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var TrickRepository
     */
    private $trickRepository;

    public function __construct(
        Environment $twig,
        CommentRepository $commentRepository,
        TrickRepository $trickRepository
    )
    {   
        $this->twig = $twig;
        $this->commentRepository = $commentRepository;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/{slug}/loadMoreComments", name="trick_loadMoreComments")
     */
    public function loadMoreComments(Request $request)
    {   
        $currentLimit = $request->request->get('currentLimit');
        $numberCommentsLoadMore = $request->request->get('numberCommentsLoadMore');

        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));
        $comments = $this->commentRepository->findByTrick($trick, ['createdAt' => 'DESC'], $numberCommentsLoadMore, $currentLimit);


        return new Response($this->twig->render(
            'tricks/commentsDisplay.html.twig', [
            'comments' => $comments
        ]));
        
    }
}
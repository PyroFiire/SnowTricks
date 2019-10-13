<?php

namespace App\Controller\Tricks\Ajax;

use Twig\Environment;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoadMoreTricks
{
    /**
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(
        Environment $twig,
        TrickRepository $trickRepository
    )
    {   
        $this->twig = $twig;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/loadMore", name="trick_loadMore")
     */
    public function loadMoreTricks(Request $request)
    {   
        $currentLimit = $request->request->get('currentLimit');
        $numberTricksLoadMore = $request->request->get('numberTricksLoadMore');
        $tricks = $this->trickRepository->findBy([],['createdAt' => 'DESC'],$numberTricksLoadMore, $currentLimit);

        return new Response($this->twig->render(
            'tricks/tricksDisplay.html.twig', [
            'tricks' => $tricks
        ]));
        
    }
}
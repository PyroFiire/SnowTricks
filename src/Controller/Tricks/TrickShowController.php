<?php

namespace App\Controller\Tricks;

use Twig\Environment;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\TrickRepository;

class TrickShowController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var TrickRepository
     */
    private $trickRepository;
    
    public function __construct(
        Environment $twig,
        TrickRepository $trickRepository
    )
    {
        $this->twig = $twig;
        $this->trickRepository = $trickRepository;
    }

    /**
     * @Route("/tricks/{slug}", name="trick_show")
     */
    public function trick_show(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));

        return new Response($this->twig->render(
            'tricks/trick.html.twig', [
            'trick' => $trick
        ]));
    }
}
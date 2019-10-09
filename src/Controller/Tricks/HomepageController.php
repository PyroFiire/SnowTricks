<?php

namespace App\Controller\Tricks;

use Twig\Environment;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

use App\Repository\TrickRepository;
class HomepageController
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
     * @Route("/", name="index")
     */
    public function homepage()
    {
        $tricks = $this->trickRepository->findAll();

        return new Response($this->twig->render(
            'tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]));
    }
}
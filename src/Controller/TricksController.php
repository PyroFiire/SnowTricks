<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Trick;
use App\Repository\TrickRepository;

class TricksController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->redirectToRoute('tricks');
    }

    /**
     * @Route("/tricks", name="tricks")
     */
    public function homepage(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/trick/{title}", name="trick_show")
     */
    public function show(TrickRepository $trickRepository, $title)
    {
        $trick = $trickRepository->findOneByTitle($title = str_replace("_", " ", $title));

        return $this->render('tricks/trick.html.twig', [
            'trick' => $trick
        ]);
    }
}

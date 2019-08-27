<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Trick;

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
    public function homepage()
    {
        $trickRepository = $this->getDoctrine()->getRepository(Trick::class);

        $tricks = $trickRepository->findAll();

        return $this->render('tricks/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }
}

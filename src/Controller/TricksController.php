<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('tricks/tricks.html.twig');
    }
}

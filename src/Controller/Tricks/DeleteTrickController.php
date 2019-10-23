<?php

namespace App\Controller\Tricks;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Doctrine\ORM\EntityManagerInterface;

use App\Repository\TrickRepository;

class DeleteTrickController
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
     * @var TrickRepository
     */
    private $trickRepository;

    public function __construct(
        UrlGeneratorInterface $router,
        TrickRepository $trickRepository,
        EntityManagerInterface $manager
    )
    {
        $this->router = $router;
        $this->trickRepository = $trickRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/tricks/delete/{slug}", name="trick_delete")
     */
    public function trickDelete(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));

        $this->manager->remove($trick);
        $this->manager->flush();

        $request->getSession()->getFlashBag()->add(
            'Notice',
            'Your Trick has been deleted !'
        );
        return new RedirectResponse($this->router->generate('index'));
    }
}
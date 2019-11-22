<?php

namespace App\Controller\Tricks;

use App\Entity\Video;
use App\Entity\Picture;

use App\Repository\MediaRepository;
use App\Repository\TrickRepository;

use Psr\Container\ContainerInterface;

use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        UrlGeneratorInterface $router,
        TrickRepository $trickRepository,
        MediaRepository $mediaRepository,
        EntityManagerInterface $manager,
        ContainerInterface $container,
        Filesystem $filesystem
    )
    {
        $this->router = $router;
        $this->container = $container;
        $this->trickRepository = $trickRepository;
        $this->mediaRepository = $mediaRepository;
        $this->manager = $manager;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route("/tricks/delete/{slug}", name="trick_delete")
     */
    public function trickDelete(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));
        $medias = $this->mediaRepository->findBy(['trick'=> $trick->getId()]);

        $spotlightPicturePath = $trick->getSpotlightPicturePath();
        if($spotlightPicturePath !== null){
            $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$spotlightPicturePath]);
        }

        foreach ($medias as $media) {
            if($media instanceof Picture){
                $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$media->getPath()]);
            }
        }

        $this->manager->remove($trick);
        $this->manager->flush();

        $request->getSession()->getFlashBag()->add(
            'Notice',
            'Your Trick has been deleted !'
        );
        return new RedirectResponse($this->router->generate('index'));
    }
}
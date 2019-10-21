<?php

namespace App\Controller\Tricks;

use App\Entity\Media;

use App\Entity\Trick;
use Twig\Environment;

use App\Form\TrickType;
use App\Repository\TrickRepository;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Filesystem\Filesystem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\File\File;

class CreateTrickController
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        EntityManagerInterface $manager,
        TrickRepository $trickRepository,
        Filesystem $filesystem,
        ContainerInterface $container
        )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->trickRepository = $trickRepository;
        $this->manager = $manager;
        $this->form = $form;
        $this->filesystem = $filesystem;
        $this->container = $container;
    }

    /**
     * @Route("/tricks/create/", name="trick_create")
     */
    public function createTrick(Request $request)
    {
        $formTrick = $this->form->create(TrickType::class, $trick = new Trick());
        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid()){

            $trick->setSlug($trick->getTitle());
            $trick->setCreatedAt(new \DateTime());

            $fileSpotlight = $formTrick->get('spotlightPicturePath')->get('file')->getData();
            if($fileSpotlight !== null) {
                $nameSpotlight = uniqid().'.'. $fileSpotlight->guessExtension();
                $trick->setSpotlightPicturePath($nameSpotlight);
                $fileSpotlight->move($this->container->getParameter('medias_directory'), $nameSpotlight );
            }
            $this->manager->persist($trick);
            
            $videosSite = [
                'youtube' => 'https://www.youtube.com',
                'dailymotion' => 'https://www.dailymotion.com',
            ];

            foreach ($formTrick->get('videos') as $videoForm) {
                $url = $videoForm->get('url')->getData();
                $video = $videoForm->getData();
                $parseUrl = parse_url($url);
                $host = $parseUrl['scheme'].'://'.$parseUrl['host'];
                parse_str($parseUrl['query'], $parseParams);
                
                if(in_array($host, $videosSite, true)) {

                    if('youtube' === $videoSite = array_search($host, $videosSite)) {
                        $name = $parseParams['v'];
                        $video->setFormat($videoSite);
                        $video->setName($name);
                        $video->setUrlEmbed($host.'/embed/'.$name);
                    }
                    if('dailymotion' === $videoSite = array_search($host, $videosSite)) {
                        $name = substr(strrchr($parseUrl['path'], '/'), 1);
                        $video->setFormat($videoSite);
                        $video->setName($name);
                        $video->setUrlEmbed($host.'/embed/video/'.$name);
                    }
                    $video->setTrick($trick);
                    $this->manager->persist($video);
                }                
            }

            foreach ($formTrick->get('pictures') as $pictureForm) {
                $picture = $pictureForm->getData();
                $file = $pictureForm->get('file')->getData();
                $path= uniqid().'.'. $file->guessExtension();

                $picture->setTrick($trick);
                $picture->setPath($path);
                $this->manager->persist($picture);
                $file->move($this->container->getParameter('medias_directory'), $path );
            }

            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'trick_show',
                ['slug' => $trick->getSlug()]
            ));
        }

        return new Response($this->twig->render(
            'tricks/createTrick.html.twig', [
            'trick' => $trick,
            'formTrick' => $formTrick->createView()        
        ]));

    }

}

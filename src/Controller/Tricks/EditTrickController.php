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

class EditTrickController
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
     * @Route("/tricks/edit/{slug}", name="trick_edit")
     */
    public function editTrick(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));
        $initialTrick = clone $trick;   
        $this->manager->refresh($trick);

        $formTrick = $this->form->create(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid()){
            // if($trick->getSpotlightPicturePath()){
            //     $trick->setSpotlightPicturePath($trick->getSpotlightPicturePath());
            // }

            $trick->setSlug($trick->getTitle());
            $trick->setModifiedAt(new \DateTime());
            
            foreach ($trick->getMedias() as $media) {
                $media->setCreatedAt(new \DateTime());
                $trick->addMedia($media);
            }
            $trick->setFileSpotlightPicturePath($formTrick['fileSpotlightPicturePath']->getData());
            try {
                if($trick->getSpotlightPicturePath() !== $initialTrick->getSpotlightPicturePath()){
                    //alors ya eu changement
                    $trick->getFileSpotlightPicturePath()->move($this->container->getParameter('medias_directory'), $trick->getSpotlightPicturePath() );
                    //efface l'ancienne image si elle existe
                    if($initialTrick->getSpotlightPicturePath() !== null){
                        $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$initialTrick->getSpotlightPicturePath()]);
                    }
                }
                else{
                    //sinon ya pas eu de changement
                }
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $this->manager->persist($trick);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'trick_show',
                ['slug' => $trick->getSlug()]
            ));
        }

        return new Response($this->twig->render(
            'tricks/editTrick.html.twig', [
            'trick' => $trick,
            'formTrick' => $formTrick->createView()        
        ]));

    }

}

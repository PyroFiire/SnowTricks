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

class FormTrickController
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
     * @Route("/tricks/edit/{slug}", name="trick_edit")
     */
    public function formTrick(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));
        
        if($trick){
            $lastSpotlightPicturePath = $trick->getSpotlightPicturePath();
            
        $fileSpotlightPicturePath = new File($this->container->getParameter('medias_directory').'/'.$trick->getSpotlightPicturePath());
        $trick->setFileSpotlightPicturePath($fileSpotlightPicturePath);
        

        }else{
            $trick = new Trick();
            $lastSpotlightPicturePath = 'create';
        }

        $formTrick = $this->form->create(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid()){
            $trick->setSlug($trick->getTitle());
            if(!$trick->getCreatedAt()){
                $trick->setCreatedAt(new \DateTime());
            }else{
                $trick->setModifiedAt(new \DateTime());
            }
            foreach ($trick->getMedias() as $media) {

                $media->setCreatedAt(new \DateTime());
                $trick->addMedia($media);

            }

            if($formTrick['fileSpotlightPicturePath']->getData()){
                $newPicturePath = $formTrick['fileSpotlightPicturePath']->getData();
            }
            elseif($trick->getFileSpotlightPicturePath()){
                $newPicturePath = $trick->getFileSpotlightPicturePath();
            }

            $newFileName = uniqid().'.'.$newPicturePath->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $newPicturePath->move($this->container->getParameter('medias_directory'), $newFileName );
                $this->filesystem->remove([$this->container->getParameter('medias_directory').'/'.$lastSpotlightPicturePath]);
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }
            $trick->setSpotlightPicturePath($newFileName);
            dump($trick);
            $this->manager->persist($trick);
            $this->manager->flush();

            return new RedirectResponse($this->router->generate(
                'trick_show',
                ['slug' => $trick->getSlug()]
            ));
        }

        return new Response($this->twig->render(
            'tricks/formTrick.html.twig', [
            'trick' => $trick,
            'formTrick' => $formTrick->createView(),
            'editMode' => $trick->getCreatedAt() !== null
        ]));

    }

}

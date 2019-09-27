<?php

namespace App\Controller\Tricks;

use App\Entity\Media;

use App\Entity\Trick;
use Twig\Environment;

use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        EntityManagerInterface $manager,
        TrickRepository $trickRepository
    )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->trickRepository = $trickRepository;
        $this->manager = $manager;
        $this->form = $form;
    }

    /**
     * @Route("/tricks/create/", name="trick_create")
     * @Route("/tricks/edit/{slug}", name="trick_edit")
     */
    public function formTrick(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));

        if(!$trick){
            $trick = new Trick();
        }
        dump($trick);
/*
        $media1 = new Media();
        $media1->setType('')
               ->setPath('')
               ->setCreatedAt(new \DateTime());
        $trick->addMedia($media1);


        $originalMedias = new ArrayCollection();
        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($trick->getMedias() as $media) {
            $originalMedias->add($media);
        }
*/
        $formTrick = $this->form->create(TrickType::class, $trick);
        $formTrick->handleRequest($request);
        dump($trick);
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

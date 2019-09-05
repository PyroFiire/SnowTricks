<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;



class TricksController extends AbstractController
{
    
    /**
     * @Route("/", name="index")
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
     * @Route("/tricks/create", name="trick_create")
     * @Route("/tricks/{slug}/edit", name="trick_edit")
     */
    public function formTrick($slug = null, Trick $trick = null, Request $request, ObjectManager $manager, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->findOneBySlug($slug);

        if(!$trick){
            $trick = new Trick();
        }

        $formTrick = $this->createForm(TrickType::class, $trick);

        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid()){
            $trick->setSlug(str_replace(" ", "_", $trick->getTitle()));
            if(!$trick->getCreatedAt()){
                $trick->setCreatedAt(new \DateTime());
            }else{
                $trick->setModifiedAt(new \DateTime());
            }

            $manager->persist($trick);
            $manager->flush();

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug()
            ]);
        }

        return $this->render('tricks/formTrick.html.twig', [
            'trick' => $trick,
            'formTrick' => $formTrick->createView(),
            'editMode' => $trick->getCreatedAt() !== null
        ]);
    }

    /**
     * @Route("/tricks/{slug}", name="trick_show")
     */
    public function show(TrickRepository $trickRepository, $slug)
    {
        $trick = $trickRepository->findOneBySlug($slug);

        return $this->render('tricks/trick.html.twig', [
            'trick' => $trick
        ]);
    }
}

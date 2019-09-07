<?php

namespace App\Controller\Tricks;

use Twig\Environment;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;

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
     * @Route("/tricks/create", name="trick_create")
     * @Route("/tricks/{slug}/edit", name="trick_edit")
     */
    public function formTrick(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));

        if(!$trick){
            $trick = new Trick();
        }

        $formTrick = $this->form->create(TrickType::class, $trick);

        $formTrick->handleRequest($request);

        if($formTrick->isSubmitted() && $formTrick->isValid()){
            $trick->setSlug(str_replace(" ", "_", $trick->getTitle()));
            if(!$trick->getCreatedAt()){
                $trick->setCreatedAt(new \DateTime());
            }else{
                $trick->setModifiedAt(new \DateTime());
            }

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

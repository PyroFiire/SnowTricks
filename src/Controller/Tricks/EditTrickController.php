<?php

namespace App\Controller\Tricks;


use Twig\Environment;

use App\Form\TrickType;
use App\Repository\TrickRepository;
use Psr\Container\ContainerInterface;

use App\HandleForm\CreateEditTrickForm;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @var TrickRepository
     */
    private $trickRepository;

    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var CreateEditTrickForm
     */
    private $createEditTrickForm;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        TrickRepository $trickRepository,
        ContainerInterface $container,
        CreateEditTrickForm $createEditTrickForm
        )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->trickRepository = $trickRepository;
        $this->form = $form;
        $this->container = $container;
        $this->createEditTrickForm = $createEditTrickForm;

    }

    /**
     * @Route("/tricks/edit/{slug}", name="trick_edit")
     */
    public function editTrick(Request $request)
    {
        $trick = $this->trickRepository->findOneBySlug($request->attributes->get('slug'));
        $formTrick = $this->form->create(TrickType::class, $trick);
        $formTrick->handleRequest($request);

        if ($this->createEditTrickForm->handleForm($formTrick, $trick)) {
            return new RedirectResponse($this->router->generate(
                'trick_show',
                ['slug' => $trick->getSlug()]
            ));
        } else {
            return new Response($this->twig->render(
                'tricks/editTrick.html.twig', [
                'trick' => $trick,
                'formTrick' => $formTrick->createView()        
            ]));
        }

    }

}

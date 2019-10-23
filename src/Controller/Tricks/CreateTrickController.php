<?php

namespace App\Controller\Tricks;

use App\Entity\Trick;
use Twig\Environment;

use App\Form\TrickType;
use App\HandleForm\CreateEditTrickForm;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
     * @var CreateEditTrickForm
     */
    private $createEditTrickForm;

    public function __construct(
        UrlGeneratorInterface $router,
        Environment $twig,
        FormFactoryInterface $form,
        CreateEditTrickForm $createEditTrickForm
        )
    {
        $this->router = $router;
        $this->twig = $twig;
        $this->form = $form;
        $this->createEditTrickForm = $createEditTrickForm;
    }

    /**
     * @Route("/tricks/create/", name="trick_create")
     */
    public function createTrick(Request $request)
    {
        $formTrick = $this->form->create(TrickType::class, $trick = new Trick());
        $formTrick->handleRequest($request);

        if ($this->createEditTrickForm->handleForm($formTrick, $trick)) {
            $request->getSession()->getFlashBag()->add(
                'Notice',
                'Your Trick has been created !'
            );
            return new RedirectResponse($this->router->generate(
                'trick_show',
                ['slug' => $trick->getSlug()]
            ));
        } else {
            return new Response($this->twig->render(
                'tricks/createTrick.html.twig', [
                'trick' => $trick,
                'formTrick' => $formTrick->createView()        
            ]));
        }

    }

}

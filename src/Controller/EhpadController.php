<?php

namespace App\Controller;

use App\Entity\Ehpad;
use App\Entity\HoraireVisio;
use App\Form\EhpadAdminType;
use App\Form\EhpadType;
use App\Repository\EhpadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ehpad")
 */
class EhpadController extends AbstractController
{
    /**
     * @Route("/", name="ehpad_index", methods={"GET"})
     */
    public function index(EhpadRepository $ehpadRepository): Response
    {
        $view = 'ehpad/index.html.twig';
        $ehpads= $ehpadRepository->findAll();
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $ehpads= $ehpadRepository->findAll();
            $view = 'admin/ehpad/index.html.twig';
        }

        return $this->render($view, [
            'ehpads' => $ehpads,
            'main' => 'ehpad',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/new", name="ehpad_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ehpad = new Ehpad();
        $formType = EhpadType::class;
        $view = 'ehpad/new.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $formType = EhpadAdminType::class;
            $view = 'admin/ehpad/new.html.twig';
        }
        $form = $this->createForm($formType, $ehpad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ehpad);
            $entityManager->flush();

            return $this->redirectToRoute('ehpad_index');
        }

        return $this->render($view, [
            'ehpad' => $ehpad,
            'main' => 'ehpad',
            'child' => 'new',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ehpad_show", methods={"GET"})
     */
    public function show(Ehpad $ehpad): Response
    {
        $view = 'ehpad/show.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $view = 'admin/ehpad/show.html.twig';
        }

        return $this->render($view, [
            'ehpad' => $ehpad,
            'main' => 'ehpad',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="ehpad_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Ehpad $ehpad): Response
    {
        $formType = EhpadType::class;
        $view = 'ehpad/edit.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $formType = EhpadAdminType::class;
            $view = 'admin/ehpad/edit.html.twig';
        }
        $form = $this->createForm($formType, $ehpad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ehpad_index');
        }

        return $this->render($view, [
            'ehpad' => $ehpad,
            'form' => $form->createView(),
            'main' => 'ehpad',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("ehpadDelete/{id}", name="ehpad_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Ehpad $ehpad): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ehpad->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ehpad);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ehpad_index');
    }

}

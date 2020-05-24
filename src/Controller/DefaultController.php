<?php

namespace App\Controller;

use App\Form\ContactEhpadType;
use App\Repository\UserRepository;
use App\Service\SuperMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/checkLogin", name="defaultLogged")
     */
    public function redirectUser(){
        switch (true){
            case $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN'):
                return $this->redirectToRoute('admin');
                break;
            case $this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE'):
                return $this->redirectToRoute('employe');
                break;
            case $this->container->get('security.authorization_checker')->isGranted('ROLE_FAMILLE'):
                return $this->redirectToRoute('famille');
                break;
            case $this->container->get('security.authorization_checker')->isGranted('ROLE_RESIDENT'):
                return $this->redirectToRoute('resident');
                break;
            default:
                return $this->redirectToRoute('app_login');
        }
    }

    /**
     * @Route("/contact" ,name="contact")
     * @param SuperMailer $superMailer
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    public function contact(SuperMailer $superMailer, Request $request, UserRepository $userRepository)
    {
        $form = $this->createForm(ContactEhpadType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $emails = $userRepository->findEmailsEmployeByEhpads([$form->get('ehpad')->getData()->getId()]);
            $superMailer->formulaireContact(
                $emails,
                (string)$form->get('email')->getData(),
                (string)$form->get('sujet')->getData(),
                (string)$form->get('nom')->getData(),
                (string)$form->get('prenom')->getData(),
                (string)$form->get('message')->getData()
            );
            $this->addFlash('success', 'Votre message a bien était envoyé');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/contact.html.twig', [
            "form" => $form->createView()
        ]);
    }

}

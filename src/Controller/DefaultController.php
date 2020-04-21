<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

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
                return $this->redirectToRoute('homepage');
        }
    }


}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/test" ,name="test")
     */
    public function test()
    {
        return $this->render('test.html.twig',[
            'main' => 'ehpad',
            'child' => 'show'
        ]);
    }

}

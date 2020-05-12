<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/test" ,name="test")
     * @param MailerInterface $mailer
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function test(MailerInterface $mailer)
    {
        $email= (new Email())->from('esethFamilly@contact.com')->to('maximiliendelangle@gmail.com')->subject('mail from app')->text('bite bite')->html('<p>Je t aime</p>');

        $mailer->send($email);
        return $this->redirectToRoute('login');
    }

}

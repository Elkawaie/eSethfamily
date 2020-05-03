<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class EmployeController extends AbstractController
{
    /**
     * @Route("/employe", name="employe")
     */
    public function index()
    {
        $employe = $this->getUser();
        $ehpad = $employe->getEhpad()->getId();
        return $this->render('employe/index.html.twig', [
            'controller_name' => 'EmployeController',
            'main' =>'nop',
            'child'=>'nop',
            'ehpad'=> $ehpad
        ]);
    }


}

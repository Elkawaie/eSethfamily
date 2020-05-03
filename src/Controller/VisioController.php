<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VisioController extends AbstractController
{
    /**
     * @Route("/visio", name="visio")
     */
    public function index()
    {
        return $this->render('visio/index.html.twig', [
            'controller_name' => 'VisioController',
        ]);
    }
}

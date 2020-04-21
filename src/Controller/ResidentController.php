<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResidentController extends AbstractController
{
    /**
     * @Route("/resident", name="resident")
     */
    public function index()
    {
        return $this->render('resident/index.html.twig', [
            'controller_name' => 'ResidentController',
        ]);
    }
}

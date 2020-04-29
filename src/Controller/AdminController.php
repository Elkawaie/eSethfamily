<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index()
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/newUser", name="admin_addUser", methods={"GET","POST"})
     */
    public function admin_addUser(Request $request)
    {

    }

    /**
     * @Route("/newUser", name="admin_showAllUser", methods={"GET","POST"})
     */
    public function admin_showAllUser(Request $request)
    {

    }

    /**
     * @Route("/newUser", name="admin_validateUser", methods={"GET","POST"})
     */
    public function admin_validateUser(Request $request)
    {

    }
}

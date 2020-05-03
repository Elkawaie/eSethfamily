<?php

namespace App\Controller;

use App\Entity\Ehpad;
use App\Entity\Famille;
use App\Entity\User;
use App\Form\AdminUserEditType;
use App\Form\AdminUserType;
use App\Form\AdminUserValidateType;
use App\Form\EmployeUserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
            'main' => 'nop',
            'child' => 'nop',
        ]);
    }

    /**
     * @Route("/newUser", name="admin_addUser", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin_addUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $famille = new Famille();
        $formType = AdminUserType::class;
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($formType, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $user->setRoles(array_unique([$form->get('roles')->getData()]));
            $user->setPassword($encoder->encodePassword($user,$form->get('password')->getData()));
            $user->setActif(true);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'ajout d\'un nouvel utilisateur a était effectuer avec succès');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
            'main' => 'user',
            'child' => 'new',
        ]);
    }

    /**
     * @Route("/showUsers", name="admin_showAllUser", methods={"GET","POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin_showAllUser(Request $request, UserRepository $userRepository)
    {
        return $this->render('admin/user/index.html.twig',[
            'users' => $userRepository->findAll(),
            'main' => 'user',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/validate", name="admin_validateUser", methods={"GET","POST"})
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin_validateUser(UserRepository $userRepository)
    {
        return $this->render('admin/user/validate.html.twig',[
            'users' => $userRepository->findBy(['actif'=>false]),
            'main' => 'user',
            'child' => 'validate',
        ]);
    }

    /**
     * @Route("/edit/{id}", name="admin_userEdit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin_editUser(Request $request, User $user)
    {
        $form = $this->createForm(AdminUserValidateType::class, $user);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid() ){
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'utilidateur a était bien activer');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user'=>$user,
            'main' => 'user',
            'child' => 'activate',
        ]);
    }

    /**
     * @Route("/editOne/{id}", name="admin_userEditOne", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function admin_editUserOne(Request $request, User $user)
    {
        $form = $this->createForm(AdminUserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin_showAllUser');
        }

        return $this->render('admin/user/editOne.html.twig', [
            'form' => $form->createView(),
            'main' => 'user',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/show/{id}", name="admin_userShowOne", methods={"GET","POST"})
     */
    public function admin_showUserOne(User $user)
    {
        return $this->render('admin/user/show.html.twig',[
            'user' => $user,
            'main' => 'user',
            'child' => 'show',
        ]);
    }
}

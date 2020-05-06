<?php

namespace App\Controller;

use App\Entity\Famille;
use App\Entity\User;
use App\Entity\Visio;
use App\Form\AdminUserValidateType;
use App\Form\EmployeEditFamillyType;
use App\Form\EmployeFamillyType;
use App\Repository\EhpadRepository;
use App\Repository\UserRepository;
use App\Repository\VisioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class EmployeController
 * @package App\Controller
 * @Route("/employe")
 */
class EmployeController extends AbstractController
{
    /**
     * @Route("/", name="employe")
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

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @Route("/addUser", name="employe_addUser")
     */
    public function employe_addUser(Request $request, UserPasswordEncoderInterface $encoder){
        $id = $request->get('ehpad');

        $user = new User();
        $formType = EmployeFamillyType::class;
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm($formType, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $user->setRoles(array_unique(['ROLE_FAMILLE']));
            $user->setPassword($encoder->encodePassword($user,$form->get('password')->getData()));
            $user->setActif(true);
            $famille = new Famille();
            $famille->setNom($form->get('nom')->getData());
            $famille->setPrenom($form->get('nom')->getData());
            $user->setFkFamille($famille);
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'ajout d\'un nouvel utilisateur a était effectuer avec succès');
            $this->redirectToRoute('employe');
        }
        $params = [
            'form' => $form->createView(),
            'main' => 'user',
            'child' => 'new',
        ];
        $params['ehpad']= $id;
        return $this->render('employe/user/new.html.twig',$params );
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * @Route("/showAllFamille", name="employe_showAllUser")
     */
    public function employe_showAllUser(Request $request, UserRepository $userRepository){
        $id = $request->get('ehpad');
        $params = [];
        $params['ehpad']= $id;
        $params['child']= 'show';
        $params['main']= 'user';
        $params['users'] =$userRepository->findByFamille_Ehpad($id);
        return $this->render('employe/user/index.html.twig', $params);
    }

    /**
     * @param Request $request
     * @Route("/validateUser", name="employe_validateUser")
     */
    public function employe_validateUser(Request $request, UserRepository $userRepository){
        $id = $request->get('ehpad');
        $params = [];
        $params['ehpad']= $id;
        $params['child']= 'validate';
        $params['main']= 'user';
        $params['users'] =$userRepository->findByUnactif(false);
        return $this->render('employe/user/validate.html.twig', $params);
    }

    /**
     * @param Request $request
     * @param VisioRepository $visioRepository
     * @return Response
     * @Route("/getVisioUnactif",name="visio_unactif")
     */
    public function visio_validate(Request $request, VisioRepository $visioRepository){
        $ehpad = $request->get('ehpad');
        $visios = $visioRepository->findVisoByEhpad($ehpad, false);
        $params = [
            'main' => 'user',
            'child' => 'new',
            'ehpad' => $request->get('ehpad')
        ];
        $params['visios'] = $visios;

        return $this->render('employe/visio/validate.html.twig', $params);
    }

    /**
     * @Route("/visio/showAll",name="visio_employe_index")
     * @param VisioRepository $visioRepository
     * @param Request $request
     */
    public function visio_employe_index(VisioRepository $visioRepository, Request $request){
        $id = $request->get('ehpad');
        $visios = $visioRepository->findVisoByEhpad($id, true);
        $params = [
            'main' => 'user',
            'child' => 'new',
            'ehpad' => $request->get('ehpad')
        ];
        $params['visios'] = $visios;
        return $this->render('employe/visio/visioValider.html.twig', $params);
    }
    /**
     * @Route("/validation_visio/{id}", name="visio_validation")
     * @param Visio $visio
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function visio_validation(Visio $visio, Request $request){
        $em = $this->getDoctrine()->getManager();
        $visio->setActif(true);
        $em->persist($visio);
        $em->flush();
        return $this->redirectToRoute('visio_unactif', ['ehpad'=> $request->get('ehpad')]);
    }

    /**
     * @Route("/edit/{id}", name="employe_userEdit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employe_editUser(Request $request, User $user)
    {

        $form = $this->createForm(AdminUserValidateType::class, $user);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if($form->isSubmitted() && $form->isValid() ){
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'L\'utilidateur a était bien activer');
            return $this->redirectToRoute('employe_validateUser', ['ehpad'=>$request->get('ehpad')]);
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user'=>$user,
            'main' => 'user',
            'child' => 'activate',
            'ehpad' => $request->get('ehpad')
        ]);
    }

    /**
     * @Route("/editOne/{id}", name="employe_userEditOne", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function employe_editUserOne(Request $request, User $user)
    {
        $famille = $user->getFkFamille();
        $form = $this->createForm(EmployeEditFamillyType::class, $user, ["famille" =>$famille]);
        $form->handleRequest($request);
        $id = $request->get('ehpad');
        if ($form->isSubmitted() && $form->isValid()) {
            $famille->setNom($form->get('nom')->getData());
            $famille->setPrenom($form->get('nom')->getData());
            $user->setFkFamille($famille);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('employe_showAllUser', ['ehpad'=> $id]);
        }

        return $this->render('employe/user/editOne.html.twig', [
            'form' => $form->createView(),
            'main' => 'user',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/show/{id}", name="employe_userShowOne", methods={"GET","POST"})
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function employe_showUserOne(User $user, Request $request)
    {
        return $this->render('employe/user/show.html.twig',[
            'user' => $user,
            'main' => 'user',
            'child' => 'show',
            'ehpad' => $request->get('ehpad')
        ]);
    }


}

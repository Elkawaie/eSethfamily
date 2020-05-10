<?php

namespace App\Controller;

use App\Entity\DemandeAdd;
use App\Entity\Ehpad;
use App\Entity\Famille;
use App\Entity\User;
use App\Entity\Visio;
use App\Form\AdminUserValidateType;
use App\Form\EmployeEditFamillyType;
use App\Form\EmployeFamillyType;
use App\Repository\DemandeAddRepository;
use App\Repository\EhpadRepository;
use App\Repository\FamilleRepository;
use App\Repository\UserRepository;
use App\Repository\VisioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
            $user->setPassword($encoder->encodePassword($user, '1234567'));
            $user->setActif(true);
            $famille = new Famille();
            $famille->setNom($form->get('nom')->getData());
            $famille->setPrenom($form->get('nom')->getData());
            $ehpad = $this->getDoctrine()->getRepository(Ehpad::class)->find($request->get('ehpad'));
            $famille->addEhpad($ehpad);
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
     * @return Response
     */
    public function employe_validateUser(Request $request, UserRepository $userRepository){
        $id = $request->get('ehpad');
        $params = [];
        $params['ehpad']= $id;
        $params['child']= 'validate';
        $params['main']= 'user';
        $params['users'] =$userRepository->findByUnactif(false, $id);
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
            'main' => 'Rdv',
            'child' => 'unactif',
            'ehpad' => $request->get('ehpad')
        ];
        $params['visios'] = $visios;

        return $this->render('employe/visio/validate.html.twig', $params);
    }

    /**
     * @param Request $request
     * @param DemandeAddRepository $demandeAddRepository
     * @return Response
     * @Route("/show_DemandesEhpad", name="show_DemandesEhpad")
     */
    public function show_DemandesEhpad(Request $request, DemandeAddRepository $demandeAddRepository)
    {
        $id = $request->get('ehpad');
        $demandes = $demandeAddRepository->findDemandeByEhpad($id, 'Ehpad');
        $params = [
            'main' => 'Demandes',
            'child' => 'ehpad',
            'ehpad' => $request->get('ehpad'),
            'demandes' => $demandes
        ];
        return $this->render('employe/demandes/ehpad.html.twig', $params);
    }

    /**
     * @param Request $request
     * @param DemandeAddRepository $demandeAddRepository
     * @return Response
     * @Route("/show_DemandesResident", name="show_DemandesResident")
     */
    public function show_DemandesResident(Request $request, DemandeAddRepository $demandeAddRepository)
    {
        $id = $request->get('ehpad');
        $demandes = $demandeAddRepository->findDemandeByEhpad($id, 'Resident');
        $params = [
            'main' => 'Demandes',
            'child' => 'resident',
            'ehpad' => $request->get('ehpad'),
            'demandes' => $demandes
        ];
        return $this->render('employe/demandes/ehpad.html.twig', $params);
    }

    /**
     * @Route("/deleteDemande/{id}", name="deleteDemande")
     * @param DemandeAdd $demandeAdd
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteDemande(DemandeAdd $demandeAdd, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $demandeAdd->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($demandeAdd);
            $entityManager->flush();
            $this->addFlash('success', 'La demande a bien était supprimer');
        }
        return $this->redirectToRoute('employe');
    }

    /**
     * @route("/validateDemande/{id}",name="validateDemande")
     * @param DemandeAdd $demandeAdd
     * @param Request $request
     * @return RedirectResponse
     */
    public function validateDemande(DemandeAdd $demandeAdd, Request $request)
    {
        if ($this->isCsrfTokenValid('valider' . $demandeAdd->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $demandeAdd->setValidate(true);
            $entityManager->persist($demandeAdd);
            $entityManager->flush();
            $this->addFlash("success", "La demande a bien était validée");
        }
        return $this->redirectToRoute('employe');
    }

    /**
     * @Route("/visio/showAll",name="visio_employe_index")
     * @param VisioRepository $visioRepository
     * @param Request $request
     * @return Response
     */
    public function visio_employe_index(VisioRepository $visioRepository, Request $request){
        $id = $request->get('ehpad');
        $visios = $visioRepository->findVisoByEhpad($id, true);
        $params = [
            'main' => 'Rdv',
            'child' => 'validate',
            'ehpad' => $request->get('ehpad')
        ];
        $params['visios'] = $visios;
        return $this->render('employe/visio/visioValider.html.twig', $params);
    }

    /**
     * @Route("/validation_visio/{id}", name="visio_validation")
     * @param Visio $visio
     * @param Request $request
     * @return RedirectResponse
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
        if ($this->isCsrfTokenValid('valider' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setActif(true);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien était valider');
        }

        return $this->redirectToRoute('employe');
    }

    /**
     * @Route("/delete/{id}", name="employe_userDelete")
     * @param User $user
     * @param Request $request
     * @return RedirectResponse
     */
    public function employe_userDelete(User $user,Request$request){
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien était supprimer');
        }

        return $this->redirectToRoute('employe');
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

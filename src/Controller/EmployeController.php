<?php

namespace App\Controller;

use App\Entity\DemandeAdd;
use App\Entity\Ehpad;
use App\Entity\Famille;
use App\Entity\HoraireVisio;
use App\Entity\User;
use App\Entity\Visio;
use App\Form\AdminUserValidateType;
use App\Form\EmployeEditFamillyType;
use App\Form\EmployeFamillyType;
use App\Form\HoraireVisioType;
use App\Form\LiaisonType;
use App\Repository\DemandeAddRepository;
use App\Repository\EhpadRepository;
use App\Repository\FamilleRepository;
use App\Repository\HoraireVisioRepository;
use App\Repository\UserRepository;
use App\Repository\VisioRepository;
use App\Service\SuperMailer;
use DateTime;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Date;

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
     * @Route("/lier", name="lierResidentFamille")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function lierResidentFamille(Request $request)
    {
        $id = $this->getUser()->getEhpad()->getId();
        $form = $this->createForm(LiaisonType::class, [$id]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $famille = $form->get('famille')->getData();
            $resident = $form->get('resident')->getData();
            $famille->addResident($resident);
            $em = $this->getDoctrine()->getManager();
            $em->persist($famille);
            $em->flush();
            $this->addFlash('success','La liaison a bien était effectué');
            return $this->redirectToRoute('employe');
        }
        return $this->render('employe/demandes/lier.html.twig', [
            'controller_name' => 'EmployeController',
            'form' => $form->createView(),
            'main' =>'Demandes',
            'child'=>'lier',
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
     * @param UserRepository $userRepository
     * @return Response
     * @Route("/validateUser", name="employe_validateUser")
     */
    public function employe_validateUser(Request $request, UserRepository $userRepository){
        $id = $request->get('ehpad');
        $user = $userRepository->findByUnactif(false, $id);
        $params = [];
        $params['ehpad']= $id;
        $params['child']= 'validate';
        $params['main']= 'user';
        $params['users'] = $user;


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
     * @Route("/addHoraireVisio", name="visio_horaire")
     * @param Request $request
     * @param EhpadRepository $ehpadRepository
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function addHoraireVisio(Request $request, EhpadRepository $ehpadRepository){
        $form = $this->createForm(HoraireVisioType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $horaire = new HoraireVisio();
            $date = new DateTime($request->request->get('horaire_visio')['date']);
            $debut = $form->get('debut')->getData();
            $fin = $form->get('fin')->getData();
            $date->setTime($debut->format('H'), $debut->format('i'));
            $date_debut = new DateTime($date->format('y-m-d').' '.$debut->format('H:i'));
            $date_fin = new DateTime($date->format('y-m-d').' '.$fin->format('H:i'));

            $horaire->setDebut($date_debut);
            $horaire->setFin($date_fin);
            $horaire->setEhpad($ehpadRepository->find($this->getUser()->getEhpad()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($horaire);
            $em->flush();
            $this->addFlash('success', 'L\'horaire pour votre ehpad a était ajoutée avec succés');
            return $this->redirectToRoute('employe');
        }
        $params = [
            'main' => 'Rdv',
            'child' => 'horaire',
            'form' => $form->createView()
        ];

        return $this->render('employe/visio/horaire.html.twig',$params
        );
    }

    /**
     * @Route("/getHoraireVisio", name="visio_getHoraire")
     * @param HoraireVisioRepository $horaireVisioRepository
     * @return Response
     */
    public function visioGetHoraire(HoraireVisioRepository $horaireVisioRepository){

        $id = $this->getUser()->getEhpad()->getId();
        $datetime = new DateTime('now');
        $horaires = $horaireVisioRepository->findByDateAndEhpad($id, $datetime);
        $params = [
            'main' => 'Rdv',
            'child' => 'voir',
            'horaires' => $horaires
        ];
        return $this->render('employe/visio/getHoraire.html.twig', $params);
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
     * @param SuperMailer $mailer
     * @return RedirectResponse
     */
    public function visio_validation(Visio $visio, Request $request, SuperMailer $mailer){
        $em = $this->getDoctrine()->getManager();
        $visio->setActif(true);
        $em->persist($visio);
        $em->flush();
        $users = $visio->getParticipant()->getValues();
        $mailer->visioValider($users);
        return $this->redirectToRoute('visio_unactif', ['ehpad'=> $request->get('ehpad')]);
    }

    /**
     * @Route("/edit/{id}", name="employe_userEdit", methods={"GET","POST"})
     * @param Request $request
     * @param User $user
     * @param SuperMailer $mailer
     * @return Response
     */
    public function employe_editUser(Request $request, User $user, SuperMailer $mailer)
    {
        if ($this->isCsrfTokenValid('valider' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setActif(true);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a bien était valider');
            $data = $user->getFkFamille();
            $mailer->compteValider($user->getEmail());

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
     * @return Response
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

    /**
     * @param HoraireVisio $horaire
     * @param Request $request
     * @Route("horaireDelete/{id}", name="horaire_delete", methods={"DELETE"})
     * @return RedirectResponse
     */
    public function horaireDelete(HoraireVisio $horaire, Request $request){
        if ($this->isCsrfTokenValid('delete'.$horaire->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($horaire);
            $entityManager->flush();
        }
        return $this->redirectToRoute('employe');
    }

}

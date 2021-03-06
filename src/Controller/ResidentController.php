<?php

namespace App\Controller;

use App\Entity\Ehpad;
use App\Entity\Resident;
use App\Entity\User;
use App\Form\ExcelFormType;
use App\Form\ResidentAdminType;
use App\Form\ResidentType;
use App\Repository\EhpadRepository;
use App\Repository\ResidentRepository;
use App\Repository\VisioRepository;
use App\Service\Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/resident")
 */
class ResidentController extends AbstractController
{
    /**
     * @Route("/", name="resident_index", methods={"GET"})
     * @param ResidentRepository $residentRepository
     * @param Request $request
     * @return Response
     */
    public function index(ResidentRepository $residentRepository,Request $request): Response
    {
        $params = [
            'main' => 'resident',
            'child' => 'show',
        ];
        $data = $residentRepository->findAll();
        $view = 'resident/index.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')){
            if($request->get('ehpad') != ''){
                $id = $request->get('ehpad');
            }else{
                $id = $this->getUser()->getEhpad()->getId();
            }
            $view = 'employe/resident/index.html.twig';
            $data = $residentRepository->findBy(['ehpad'=> $id]);
            $params['ehpad'] = $id;
        }
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $view = 'admin/resident/index.html.twig';
            $data = $residentRepository->findAll();
        }
        $params['residents'] = $data;
        return $this->render($view, $params);
    }

    /**
     * @Route("/new", name="resident_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $userPasswordEncoder): Response
    {
        $id='';
        $resident = new Resident();
        $formType = ResidentType::class;
        $view = 'resident/new.html.twig';
        $entityManager = $this->getDoctrine()->getManager();
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $formType = ResidentAdminType::class;
            $view = 'admin/resident/new.html.twig';

        }
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')){
            $formType = ResidentType::class;
            $view = 'employe/resident/new.html.twig';
            $id = $request->get('ehpad');
            $ehpad = $entityManager->getRepository(Ehpad::class)->find($id);
            $resident->setEhpad($ehpad);
        }
        $form = $this->createForm($formType, $resident);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
                $resident->setEhpad($form->get('ehpad')->getData());
                $resident->setFamille($form->get('famille')->getData());
            }
            $resident->setNumResident($form->get('numResident')->getData());
            $user = new User();
            $user->setRoles(array_unique(['ROLE_RESIDENT']));
            $user->setPassword($userPasswordEncoder->encodePassword($user,'1234567'));
            $user->setEmail(substr($form->get('nom')->getData(),0 , 3).'.'.substr($form->get('prenom')->getData(),0,3).'@esethfamily.com');
            $resident->setUser($user);
            $entityManager->persist($resident);
            $entityManager->flush();

            return $this->redirectToRoute('resident_index', ['ehpad' => $id]);
        }

        return $this->render($view, [
            'resident' => $resident,
            'form' => $form->createView(),
            'main' => 'resident',
            'child' => 'new',
        ]);
    }

    /**
     * @Route("/setExcel", name="resident_setExcel")
     * @param Request $request
     * @param Uploader $uploader
     * @param EhpadRepository $ehpadRepository
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @return Response
     */
    public function setExcel(Request $request, Uploader $uploader, EhpadRepository $ehpadRepository, UserPasswordEncoderInterface $userPasswordEncoder){
        $form = $this->createForm(ExcelFormType::class);
        $form->handleRequest($request);
        $params = [
            'form'=>$form->createView(),
            'main' => 'resident',
            'child' => 'excel',
        ];
        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('fichier')->getData();
            if($file){
                $em = $this->getDoctrine()->getManager();
                $filename = $uploader->upload($file);
                $datas = file($uploader->getTargetDirectory().'/'.$filename);
                $ehpad = $ehpadRepository->find($request->get('ehpad'));
                for($i = 1; $i < count($datas); $i++ ){
                    $resident_array =  preg_split ("/\,/", $datas[$i]);
                    if($em->getRepository(Resident::class)->findBy(['numResident'=> $resident_array[2]])){
                        $resident = $em->getRepository(Resident::class)->findBy(['numResident'=> $resident_array[2]]);
                        if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')) {
                            $resident[0]->setEhpad($ehpad);
                            $resident[0]->setNom($resident_array[0]);
                            $resident[0]->setPrenom($resident_array[1]);
                            $resident[0]->setNumResident($resident_array[2]);
                            $resident[0]->setNumChambre($resident_array[3]);
                        }
                        $em->persist($resident[0]);
                    }else{
                        $resident = new Resident();
                        if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')) {
                            $resident->setEhpad($ehpad);
                            $resident->setNom($resident_array[0]);
                            $resident->setPrenom($resident_array[1]);
                            $resident->setNumResident($resident_array[2]);
                            $resident->setNumChambre($resident_array[3]);
                            $user = new User();
                            $user->setRoles(array_unique(['ROLE_RESIDENT']));
                            $user->setActif(true);
                            $user->setPassword($userPasswordEncoder->encodePassword($user,'1234567'));
                            $user->setEmail(strtolower(substr($resident_array[0],0 , 4)).'.'.substr($resident_array[1],0,4).'@eseth.com');
                            $resident->setUser($user);
                            $em->persist($user);
                        }
                        $em->persist($resident);
                    }

                    $em->flush();
                }
            }
            if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')){
                $params['ehpad'] = $request->get('ehpad');
            }
        }
        return $this->render('admin/resident/setExcel.html.twig', $params);
    }

    /**
     * @Route("/getExcel",name="resident_getExcel")
     */
    public function getExcel(){
        return $this->render('admin/resident/getExcel.html.twig', [
            'main' => 'resident',
            'child' => 'getexcel',
        ]);
    }

    /**
     * @Route("/{id}", name="resident_show", methods={"GET"})
     */
    public function show(Resident $resident): Response
    {
        $view = 'resident/show.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $view = 'admin/resident/show.html.twig';
        }
        return $this->render($view, [
            'resident' => $resident,
            'main' => 'resident',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/resident/Dashboard", name="resident")
     * @param VisioRepository $visioRepository
     * @return Response
     */
    public function residentDashboard(VisioRepository $visioRepository){
        $resident = $this->getUser()->getFkResident();
        $visio = $visioRepository->findVisoByResident($resident->getId());
        $params = [
            'main' => 'resident',
            'child' => 'show',
        ];
        $params['visios'] = $visio;
        return $this->render('resident/dashboard/index.html.twig', $params);
    }

    /**
     * @Route("/{id}/edit", name="resident_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Resident $resident): Response
    {

        $formtype= ResidentType::class;
        $view = 'resident/edit.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $view = 'admin/resident/edit.html.twig';
            $formtype= ResidentAdminType::class;
        }
        $form = $this->createForm($formtype, $resident);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resident_index');
        }

        return $this->render($view, [
            'resident' => $resident,
            'form' => $form->createView(),
            'main' => 'resident',
            'child' => 'show',
        ]);
    }

    /**
     * @Route("/{id}", name="resident_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Resident $resident): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resident->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resident);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resident_index');
    }

}

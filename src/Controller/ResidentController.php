<?php

namespace App\Controller;

use App\Entity\Ehpad;
use App\Entity\Resident;
use App\Form\ExcelFormType;
use App\Form\ResidentAdminType;
use App\Form\ResidentType;
use App\Repository\ResidentRepository;
use App\Service\Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/resident")
 */
class ResidentController extends AbstractController
{
    /**
     * @Route("/", name="resident_index", methods={"GET"})
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
                $this->addFlash('error', 'Une erreur est survenue merci de bien vouloir ressayé dans quelques minutes');
                return $this->redirectToRoute('employe');
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
     */
    public function new(Request $request): Response
    {
        $id = '';
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
     * @return Response
     */
    public function setExcel(Request $request, Uploader $uploader){
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
                if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')){
                    $repo = $em->getRepository(Ehpad::class);
                    $ehpad = $repo->find($request->get('ehpad'));
                }

                $filename = $uploader->upload($file);
                $datas = file($uploader->getTargetDirectory().'/'.$filename);
                dump($datas);
                for($i = 1; $i < count($datas); $i++ ){
                    $resident_array =  preg_split ("/\,/", $datas[$i]);
                    if($em->getRepository(Resident::class)->findBy(['numResident'=> $resident_array[2]])){
                        $resident = $em->getRepository(Resident::class)->findBy(['numResident'=> $resident_array[2]]);
                    }else{
                        $resident = new Resident();
                    }
                    if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')) {
                        $resident->setEhpad($ehpad);
                    }

                    $resident->setNom($resident_array[0]);
                    $resident->setPrenom($resident_array[1]);
                    $resident->setNumResident($resident_array[2]);
                    dump($resident);
                    $em->persist($resident);
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

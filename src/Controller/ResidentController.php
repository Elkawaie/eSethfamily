<?php

namespace App\Controller;

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
    public function index(ResidentRepository $residentRepository): Response
    {
        $data = $residentRepository->findAll();
        $view = 'resident/index.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $view = 'admin/resident/index.html.twig';
            $data = $residentRepository->findAll();
        }
        return $this->render($view, [
            'residents' => $data,
        ]);
    }

    /**
     * @Route("/new", name="resident_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resident = new Resident();
        $formType = ResidentType::class;
        $view = 'resident/new.html.twig';
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $formType = ResidentAdminType::class;
            $view = 'admin/resident/new.html.twig';
        }
        $form = $this->createForm($formType, $resident);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resident);
            $entityManager->flush();

            return $this->redirectToRoute('resident_index');
        }

        return $this->render($view, [
            'resident' => $resident,
            'form' => $form->createView(),
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

        if($form->isSubmitted() && $form->isValid()){
            $file = $form->get('fichier')->getData();
            dump($file);
            if($file){
                $filename = $uploader->upload($file);
            }
            $datas = file($uploader->getTargetDirectory().'/'.$filename);
            dump($datas);
            $em = $this->getDoctrine()->getManager();
            for($i = 1; $i < count($datas); $i++ ){
                $resident_array = $str_arr = preg_split ("/\,/", $datas[$i]);
                $resident = new Resident();
                $resident->setNom($resident_array[0]);
                $resident->setPrenom($resident_array[1]);
                $resident->setNumResident($resident_array[2]);
                $em->persist($resident);
                $em->flush();
            }
        }
        return $this->render('admin/resident/setExcel.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/getExcel",name="resident_getExcel")
     */
    public function getExcel(){

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

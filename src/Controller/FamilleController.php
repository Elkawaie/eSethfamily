<?php

namespace App\Controller;

use App\Entity\Visio;
use App\Repository\FamilleRepository;
use App\Repository\ResidentRepository;
use App\Repository\UserRepository;
use App\Repository\VisioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FamilleController
 * @package App\Controller
 * @Route("/famille")
 */
class FamilleController extends AbstractController
{
    /**
     * @Route("/", name="famille")
     */
    public function index(ResidentRepository $residentRepository)
    {
        $famille = $this->getUser()->getFkFamille();
        $resident = $residentRepository->findBy(['famille' => $famille->getId()]);
        $params = [
            'controller_name' => 'FamilleController',
            'main'=>'visio',
            'child'=> 'nop'
        ];
        if($resident != null){
            $params['residents'] = $resident;
        }else{
            $params['noResident'] = 'Pas de résident ajoutée';
        }
        return $this->render('famille/index.html.twig', $params);
    }

    /**
     * @Route("/addResident", name="addResident")
     */
    public function addResident(){

    }

    /**
     * @Route("/visios", name="visios_shows")
     * @param FamilleRepository $famillerepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAllVisios(FamilleRepository $famillerepository, Request $request){
        $params = [
            'controller_name' => 'FamilleController',
            'main'=>'visio',
            'child'=> 'nop'
        ];
        $id = $this->getUser()->getFkFamille()->getId();
        $famille = $famillerepository->findBy(['id'=> $id]);
        $visios = $famille[0]->getVisios();
        $params['visios'] = $visios->getValues();
        $params['id'] = $request->get('id');

        return $this->render('famille/visio/showAll.html.twig',$params);
    }

    /**
     * @Route("/{id}", name="visio_delete")
     * @param Visio $visio
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteVisio(Visio $visio, Request $request){
        if ($this->isCsrfTokenValid('delete'.$visio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($visio);
            $entityManager->flush();
        }
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')){
            return $this->redirectToRoute('visio_employe_index',['ehpad'=>$request->get('ehpad')]);
        };
        return $this->redirectToRoute('visios_shows', ['id'=> $this->getUser()->getFkFamille()->getId()]);
    }
}

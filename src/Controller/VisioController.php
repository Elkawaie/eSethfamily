<?php

namespace App\Controller;

use App\Entity\Resident;
use App\Entity\Visio;
use App\Form\VisioFormType;
use App\Repository\VisioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

/**
 * @Route("/visio")
 */
class VisioController extends AbstractController
{

    /**
     * @return Response
     * @Route("/", name="visio")
     */
    public function index()
    {
        return $this->render('visio/index.html.twig', [
            'controller_name' => 'VisioController',
        ]);
    }

    /**
     * @Route("/new", name="visio_new")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function visoNew(Request $request){
        $em = $this->getDoctrine()->getManager();
        $resident =  $em->getRepository(Resident::class)->find($request->get('id'));
        $participant = $this->getUser()->getFkFamille();
        $visio = new Visio();
        $visio->addResident($resident);
        $visio->setActif(false);
        $visio->addParticipant($participant);
        $form = $this->createForm(VisioFormType::class,$visio);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $date = new \DateTime($request->request->get('visio_form')['jour']);
            $minute = new \DateTime();
            $arrayTime = explode(':', $request->request->get('visio_form')['heure']);
            $minute->setTime($arrayTime[0], $arrayTime[1]);
            $startDate = new \DateTime();
            $startDate->setDate((int)$date->format('yy'), (int)$date->format('m'), (int)$date->format('d'));
            $startDate->setTime((int)$minute->format('H'), (int)$minute->format('i'));
            $visio->setStartAt($startDate);
            $endDate = new \DateTime();
            $endDate->setDate((int)$date->format('yy'), (int)$date->format('m'), (int)$date->format('d'));
            $endDate->setTime((int)$minute->format('H'), (int)$minute->format('i'));
            $visio->setCreatedAt(new \DateTime('now'));
            $visio->setEndAt($endDate->modify('+60 minutes'));
            $url = md5($resident->getNom().$startDate->format('y/m/d').$resident->getNumResident());
            $visio->setUrl($url);
            $em->persist($visio);
            $em->flush();
            return $this->redirectToRoute('visio_new', ['id'=> $request->get('id')]);
        }
        return $this->render('visio/new.html.twig',[
            'form'=>$form->createView(),
            'main'=> 'visio',
            'child'=>'new'
        ]);
    }

    /**
     * @param Request $request
     * @param VisioRepository $visioRepository
     * @return Response
     * @Route("/openVisio/{url}", name="openVisio")
     */
    public function openVisio(Request $request, VisioRepository $visioRepository)
    {
        $url = $request->get('url');
        $id = $request->get('id');
        $now = new \DateTime('now');
        $visio = $visioRepository->find($id);
        if(
            $visio->getStartAt()->modify('-5 minutes') > $now &&
            $visio->getEndAt()->modify('+5 minutes') > $now &&
            $visio->getActif() == true )
        {
           return $this->render('visio/openVisio.html.twig',[
               'main'=> 'visio',
               'child'=>'new',
               'visio' => $visio,
               'valid'=> true,
           ]);
        };
        return $this->render('visio/openVisio.html.twig',[
            'main'=> 'visio',
            'child'=>'new',
            'visio' => $visio,
            'valid' => false
        ]);
    }
}

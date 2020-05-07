<?php

namespace App\Controller;

use App\Entity\DemandeAdd;
use App\Entity\Ehpad;
use App\Entity\Visio;
use App\Repository\FamilleRepository;
use App\Repository\ResidentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @param ResidentRepository $residentRepository
     * @return Response
     */
    public function index(ResidentRepository $residentRepository)
    {
        $famille = $this->getUser()->getFkFamille();
        $resident = $residentRepository->findBy(['famille' => $famille->getId()]);
        $params = [
            'controller_name' => 'FamilleController',
            'main' => 'visio',
            'child' => 'nop'
        ];
        if ($resident != null) {
            $params['residents'] = $resident;
        } else {
            $params['noResident'] = 'Pas de résident ajoutée';
        }
        return $this->render('famille/index.html.twig', $params);
    }

    /**
     * @Route("/addResident", name="addResident")
     * @param Request $request
     * @param FamilleRepository $familleRepository
     * @return RedirectResponse|Response
     */
    public function addResident(Request $request, FamilleRepository $familleRepository)
    {
        $form = $this->createFormBuilder()
            ->add('resident', TextareaType::class,[
                "attr" => ["placeholder"=>"Merci de bien vouloir nous transmettre Nom et Prenom du resident"]
            ])->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $demande = new DemandeAdd();
            $userFkId = $this->getUser()->getFkFamille()->getId();
            $famille = $familleRepository->find($userFkId);
            $demande->setSujet('Resident');
            $demande->setIdSujet($form->get('resident')->getData());
            $demande->setDemandeur($famille);
            $demande->setValidate(false);
            $this->addFlash('success', 'Votre demande a bien était prise en compte');
            $em->persist($demande);
            $em->flush();
            return $this->redirectToRoute('famille');
        }
        $params = [
            'controller_name' => 'FamilleController',
            'main' => 'visio',
            'child' => 'nop',
            'form' => $form->createView()
        ];
        return $this->render('famille/ehpad/addResident.html.twig', $params);
    }

    /**
     * @Route("/addEhpad", name="addEhpad")
     * @param Request $request
     * @param FamilleRepository $familleRepository
     * @return Response
     */
    public function addEhpad(Request $request, FamilleRepository $familleRepository)
    {
        $form = $this->createFormBuilder()
        ->add('ehpad', EntityType::class,[
            "class"=>Ehpad::class,
            'choice_label' => 'nom'
        ])->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $demande = new DemandeAdd();
            $userFkId = $this->getUser()->getFkFamille()->getId();
            $famille = $familleRepository->find($userFkId);
            $demande->setSujet('Ehpad');
            $demande->setIdSujet($form->get('ehpad')->getData()->getId());
            $demande->setDemandeur($famille);
            $demande->setValidate(false);
            $this->addFlash('success', 'Votre demande a bien était prise en compte');
            $em->persist($demande);
            $em->flush();
            return $this->redirectToRoute('famille');
        }
        $params = [
            'controller_name' => 'FamilleController',
            'main' => 'visio',
            'child' => 'nop',
            'form' => $form->createView()
        ];
        return $this->render('famille/ehpad/addEhpad.html.twig', $params);
    }

    /**
     * @Route("/visios", name="visios_shows")
     * @param FamilleRepository $famillerepository
     * @param Request $request
     * @return Response
     */
    public function getAllVisios(FamilleRepository $famillerepository, Request $request)
    {
        $params = [
            'controller_name' => 'FamilleController',
            'main' => 'visio',
            'child' => 'nop'
        ];
        $id = $this->getUser()->getFkFamille()->getId();
        $famille = $famillerepository->findBy(['id' => $id]);
        $visios = $famille[0]->getVisios();
        $params['visios'] = $visios->getValues();
        $params['id'] = $request->get('id');

        return $this->render('famille/visio/showAll.html.twig', $params);
    }

    /**
     * @Route("/{id}", name="visio_delete")
     * @param Visio $visio
     * @param Request $request
     * @return RedirectResponse
     */
    public function deleteVisio(Visio $visio, Request $request)
    {
        if ($this->isCsrfTokenValid('delete' . $visio->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($visio);
            $entityManager->flush();
        }
        if ($this->container->get('security.authorization_checker')->isGranted('ROLE_EMPLOYE')) {
            return $this->redirectToRoute('visio_employe_index', ['ehpad' => $request->get('ehpad')]);
        };
        return $this->redirectToRoute('visios_shows', ['id' => $this->getUser()->getFkFamille()->getId()]);
    }
}

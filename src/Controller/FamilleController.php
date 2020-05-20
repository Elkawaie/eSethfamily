<?php

namespace App\Controller;

use App\Entity\DemandeAdd;
use App\Entity\Ehpad;
use App\Entity\Famille;
use App\Entity\User;
use App\Entity\Visio;
use App\Form\AddResidentType;
use App\Repository\EhpadRepository;
use App\Repository\FamilleRepository;
use App\Repository\ResidentRepository;
use App\Repository\UserRepository;
use App\Service\SuperMailer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param FamilleRepository $famillerepository
     * @param Request $request
     * @return Response
     */
    public function index(FamilleRepository $famillerepository, Request $request)
    {
        $params = [
            'controller_name' => 'FamilleController',
            'main' => 'visio',
            'child' => 'nop'
        ];
        $id = $this->getUser()->getFkFamille();
        $famille = $famillerepository->findBy(['id' => $id->getId()]);
        $visios = $famille[0]->getVisios();
        $params['visios'] = $visios->getValues();
        $params['id'] = $request->get('id');

        return $this->render('famille/visio/showAll.html.twig', $params);
    }

    /**
     * @Route("/getDates", name="getDatesFromFamille")
     * @param Request $request
     * @param ResidentRepository $residentRepository
     * @param EhpadRepository $ehpadRepository
     */
    public function getDates(Request $request, ResidentRepository $residentRepository, EhpadRepository $ehpadRepository){
        $residentId = $residentRepository->find($request->request->get('id'));
        $ehpadId = $ehpadRepository->find($residentId->getEhpad()->getId());
        $horaires = $ehpadId->getHoraireVisios()->getValues();
        $strDates = '';
        foreach ($horaires as $key => $horaire){
            dump($key);
            if($key == 0){
                $strDates .= $horaire->getDebut()->format('Y-m-d');
            }else{
                $strDates .= ', '.$horaire->getDebut()->format('Y-m-d');
            }
        }
        $data = [$strDates];
        return new JsonResponse(json_encode($data));
    }

    /**
     * @Route("/addResident", name="addResident")
     * @param Request $request
     * @param FamilleRepository $familleRepository
     * @param SuperMailer $superMailer
     * @param UserRepository $userRepository
     * @return RedirectResponse|Response
     */
    public function addResident(Request $request, FamilleRepository $familleRepository, SuperMailer $superMailer, UserRepository $userRepository)
    {
        $ehpads = $this->getUser()->getFkFamille();
        $famille = $familleRepository->find($this->getUser()->getFkFamille()->getId());
        $arrayEhpad = $famille->getEhpads()->getValues();
        $ids = [];
        foreach ($arrayEhpad as $item) {
            $ids[] = $item->getId();
        }
        $form = $this->createForm(AddResidentType::class, [$ids]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $demande = new DemandeAdd();
            $userFkId = $this->getUser()->getFkFamille()->getId();
            $famille = $familleRepository->find($userFkId);
            $demande->setSujet('Resident');
            $demande->setIdSujet($form->get('resident')->getData());
            $demande->setChoixEhpadResident($form->get('ehpad')->getData()->getId());
            $demande->setDemandeur($famille);
            $demande->setValidate(false);
            $this->addFlash('success', 'Votre demande a bien était prise en compte');
            $users = $this->getUser()->getFkFamille()->getEhpads()->getValues();
            $ids = [];
            foreach ($users as $ehpad){
                array_push($ids, $ehpad->getId());
            }
            $data = [$famille, $form->get('resident')->getData()];
            $emails = $userRepository->findEmailsEmployeByEhpads($ids);
            $superMailer->nouvelDemande('Resident', $emails, $data);
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
     * @param UserRepository $userRepository
     * @param SuperMailer $superMailer
     * @return Response
     */
    public function addEhpad(Request $request, FamilleRepository $familleRepository,UserRepository $userRepository,SuperMailer $superMailer)
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
            $users = $this->getUser()->getFkFamille()->getEhpads()->getValues();
            $ids = [];
            foreach ($users as $ehpad){
                array_push($ids, $ehpad->getId());
            }
            array_push($ids,$form->get('ehpad')->getData()->getId());
            $data = [$famille];
            $emails = $userRepository->findEmailsEmployeByEhpads($ids);
            $superMailer->nouvelDemande('Ehpad', $emails, $data);
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
     * @Route("/newVisio", name="visios_shows")
     * @param ResidentRepository $residentRepository
     * @param Request $request
     * @return Response
     */
    public function getResident(ResidentRepository $residentRepository , Request $request)
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

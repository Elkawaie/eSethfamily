<?php

namespace App\Controller;

use App\Entity\Famille;
use App\Entity\User;
use App\Form\InscriptionType;
use App\Repository\UserRepository;
use App\Service\SuperMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
//         if ($this->getUser()) {
//             return $this->redirectToRoute('homepage');
//         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     * @param SuperMailer $superMailer
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository, SuperMailer $superMailer)
    {
        $form = $this->createForm(InscriptionType::class);
        $form->handleRequest($request);
        if($request->isMethod('POST')){
            $user = new User();
            $user->setEmail($form->get('email')->getData());
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form->get('password')->getData())
            );
            $famille = new Famille();
            $famille->setNom($form->get('nom')->getData());
            $famille->setPrenom($form->get('prenom')->getData());
            $famille->setCommentaire($form->get('commentaire')->getData());
            $famille->addEhpad($form->get('ehpad')->getData());
            $user->setFkFamille($famille);
            $user->setActif(false);
            $user->setRoles(array_unique(['ROLE_FAMILLE']));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre inscription a bien était enregistrer, il est en attente de validation. A son activation vous receverais un email d\'information ');
            $idEhpad = $form->get('ehpad')->getData()->getId();
            $emails = $userRepository->findEmailsEmployeByEhpad($idEhpad);
            $superMailer->nouvelUtilistateurTypeFamille($emails);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig',[
            'form' => $form->createView()
        ]);
    }
}

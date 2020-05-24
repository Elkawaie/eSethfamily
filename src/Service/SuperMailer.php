<?php


namespace App\Service;


use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SuperMailer
{
    private $mailer;
        public function __construct(MailerInterface $mailer)
        {
            $this->mailer = $mailer;
        }

        public function nouvelUtilistateurTypeFamille($emailEmploye){
            $employe = [];
            foreach ($emailEmploye as $email){
                array_push($employe, $email["email"]);
            }
            $email= (new Email())
                ->from('eSeathFamilly@contact.fr')
                ->to(...$employe)
                ->subject('Nouvelle inscription utilisateur')
                ->html('<p>Un nouvel utilisateur ( membre d\'une famille) c\'est inscrit. <br> Merci de bien vouloir vérifier la véracité des information avant de validé ce dernier</p>');
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }

        }

        public function formulaireContact(array $emails, string $from, string $subject, string $nom, string $prenom, string $message){
            $employe = [];
            foreach ($emails as $email){
                array_push($employe, $email["email"]);
            }
            $email = (new Email())
                ->from($from)
                ->to(...$employe)
                ->subject($subject)
                ->html("<h3> Mr ou Mme ".$nom." ".$prenom." vous à adresser ce message </h3><br><br><p>".$message."</p>");
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
        }

        public function compteValider($mail){
            $email = (new Email())
                ->from('eSeathFamily@contact.fr')
                ->to($mail)
                ->subject('Validation de compte')
                ->html('<p>Mr, Mme Bonjour <br> Nous avons le plaisir de vous informer que votre compte a bien était valider. <br> Bienvenue et à bientôt sur eSeathFamily</p>');
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
        }

        public function nouvelDemande($sujet, $users, $data){
            $emails = [];
            foreach ($users as $email){
                array_push($emails, $email["email"]);
            }
            $email= (new Email())
                ->from('eSeathFamilly@contact.fr')
                ->to(...$emails)
                ->subject('Nouvel demande '.$sujet)
                ->html('<p>Une nouvel demande de mise en relation avec un '.$sujet.' a était lancée.
                        Merci de bien vouloir checker et valider si necessaire la demande de Mr ou Mme '.$data[0]->getNom().' '.$data[0]->getPrenom().'</p>');
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
        }

        public function visioValider($users){
            $emails = [];
            foreach ($users as $email){
                array_push($emails, $email->getUser()->getEmail());
            }
            $email= (new Email())
                ->from('eSeathFamilly@contact.fr')
                ->to(...$emails)
                ->subject('Rendez-vous valider')
                ->html('<p>Votre demande de rendez-vous pour une video conférence a bien était valider par l\'établissement concerné</p>');
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
            }
        }
}
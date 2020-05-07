<?php

namespace App\Form;

use App\Entity\Ehpad;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                "attr"=> ["placeholder"=> "Votre Email"]

            ])
            ->add('password', PasswordType::class,[
                "attr"=> ["placeholder"=> "Vote mot de passe"]

            ])
            ->add('nom', null, [
                "attr"=> ["placeholder" => "Votre Nom"]

            ])
            ->add('prenom', null, [
                "attr"=> ["placeholder" => "Votre Prenom"]

            ])
            ->add('ehpad', EntityType::class, [
                "class"=>Ehpad::class,
                'choice_label' => 'nom'
            ])
            ->add('commentaire', TextareaType::class, [
                "attr"=> ["placeholder" => "Merci de préciser ici, Nom et prenom du résident que vous souhaiter contacter"]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

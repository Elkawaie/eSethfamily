<?php

namespace App\Form;

use App\Entity\Ehpad;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices =[
            'ROLE_FAMILLE' => 'ROLE_FAMILLE',
            'ROLE_ADMIN' => 'ROLE_ADMIN',
            'ROLE_EMPLOYE' => 'ROLE_EMPLOYE',
            'ROLE_RESIDENT' => 'ROLE_RESIDENT',
        ];
        $builder
            ->add('email', null,[
                'attr'=> ['class'=> 'form-control email']
            ])
            ->add('roles', ChoiceType::class,[
                'choices' => $choices,
                'mapped'=> false,
                'placeholder'=>'Veuillez choisir un grade',
                'attr'=> ['class'=> 'custom-select', 'id'=>'inputGroupSelect01']
            ])
            ->add('password', PasswordType::class,[
                'attr'=> ['class'=> 'form-control']
            ])
            ->add('ehpad', EntityType::class, [
                'class'=> Ehpad::class,
                'choice_label' => 'nom',
                'attr'=> ['class'=> 'custom-select', 'id'=>'inputGroupSelect01']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

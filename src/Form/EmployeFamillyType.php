<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeFamillyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', null,[
                'attr'=> ['class'=> 'form-control email']
            ])
            ->add('nom', null, [
                'attr'=> ['class'=> 'form-control'],
                'mapped'=> false
            ])
            ->add('prenom', null, [
                'attr'=> ['class'=> 'form-control'],
                'mapped'=> false
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

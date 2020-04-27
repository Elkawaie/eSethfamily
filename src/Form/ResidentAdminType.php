<?php

namespace App\Form;

use App\Entity\Ehpad;
use App\Entity\Famille;
use App\Entity\Resident;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResidentAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('numResident')
            ->add('famille', EntityType::class, [
                'class'=> Famille::class,
                'choice_label' => 'nom'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label'=>'email'
            ])
            ->add('ehpad', EntityType::class, [
                'class' => Ehpad::class,
                'choice_label' => 'nom'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Resident::class,
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Ehpad;
use App\Entity\Famille;
use App\Entity\Resident;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResidentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom', null, [
                'label' => 'Prénom'
            ])
            ->add('numResident', null, [
                'label' => 'Numéro du Résident'
            ])
            ->add('numChambre', null, [
                'label' => 'Numéro de la chambre'
            ])
            ->add('famille', EntityType::class, [
                'class'=> Famille::class,
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

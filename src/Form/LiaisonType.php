<?php

namespace App\Form;

use App\Entity\Famille;
use App\Entity\Resident;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LiaisonType extends AbstractType
{
    private $idEhpad;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->idEhpad = $options['data'][0];
        $builder
            ->add('famille', EntityType::class, [
                'class' => Famille::class,
                'choice_label' => function($famille){
                return $famille->getNom().' '.$famille->getPrenom();
                },
                'query_builder' => function(EntityRepository $er){
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nom','ASC');
                }
            ])
            ->add('resident', EntityType::class, [
                'class' => Resident::class,
                'choice_label' => function($resident){
                    return $resident->getNom().' '.$resident->getPrenom();
                },
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.ehpad', 'Ehpad')
                        ->where('Ehpad.id = :id')
                        ->setParameter('id', $this->idEhpad)
                        ->orderBy('u.nom','ASC');
                }
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

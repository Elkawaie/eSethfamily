<?php

namespace App\Form;

use App\Entity\Visio;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisioFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('heure',TimeType::class, [
                'mapped'=> false,
                'widget'=>'single_text',
                'attr'=>['class'=>"form-control"]
            ])
            ->add('jour', DateTimeType::class, [
                'mapped'=> false,
                'required'=>false,
                'format'=> 'dd/mm/yyyy',
                'widget'=>'single_text',
                'html5'=>false,
                'attr'=>['data-provide'=>"datepicker",
                    'data-date-autoclose'=>"true",
                    'class'=>"form-control",
                    'data-date-format'=>"dd/mm/yyyy"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visio::class,
        ]);
    }
}

<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoraireVisioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, [
                'mapped'=> false,
                'required'=>false,
                'format'=> 'dd/mm/yyyy',
                'widget'=>'single_text',
                'html5'=>false,
                'attr'=>[
                    'data-provide'=>"datepicker",
                    'data-date-autoclose'=>"true",
                    'class'=>"form-control",
                    'data-date-format'=>"yyyy/mm/dd"
                ]
            ])
            ->add('debut', TimeType::class,  [
                'mapped'=> false,
                'widget'=>'single_text',
                'attr'=>['class'=>"form-control"]
            ])
            ->add('fin', TimeType::class, [
                'mapped'=> false,
                'widget'=>'single_text',
                'attr'=>['class'=>"form-control"]
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

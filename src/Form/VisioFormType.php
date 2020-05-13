<?php

namespace App\Form;

use App\Entity\HoraireVisio;
use App\Entity\Resident;
use App\Entity\Visio;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisioFormType extends AbstractType
{
    private $em;

    private $request;

    public function __construct(RequestStack $request, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->request = $request;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('heure',TimeType::class, [
            'mapped'=> false,
            'widget'=>'single_text',
            'html5'=> false,
            'attr'=>['class'=>"form-control timepicker", 'placeholder'=>"Merci d'indiquer l'heure que vous souhaitez"]
        ])
            ->add('jour', DateTimeType::class, [
                'mapped'=> false,
                'required'=>false,
                'format'=> 'yyyy/mm/dd',
                'widget'=>'single_text',
                'html5'=>false,
                'attr'=>['data-provide'=>"datepicker",
                    'data-date-autoclose'=>"true",
                    'class'=>"form-control datepicker",
                    'data-date-format'=>"yyyy/mm/dd"
                ]
            ])
        ;
        $builder->get('jour')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event){
            $form = $event->getForm();
            $date = $this->request->getCurrentRequest()->request->get('visio_form')['jour'];
            $date_debut = new DateTime($date);
            $date_fin = new DateTime($date);
            $date_fin->setTime(23,59);
            $id = $this->em->getRepository(Resident::class)->find($this->request->getCurrentRequest()->get('id'))->getEhpad();
            $horaires = $this->em->getRepository(HoraireVisio::class)->findHoraireByEhpadAndDate($date_debut,$date_fin,$id );
            $datas ='';
            foreach ($horaires as $horaire){
                $datas .= $horaire->getDebut()->format('H:i').'|'.$horaire->getFin()->format('H:i').';';
            }
            $form->getParent()->add('heure',TimeType::class, [
                'mapped'=> false,
                'widget'=>'single_text',
                'auto_initialize' => false,
                'html5'=> false,
                'attr'=>['class'=>"form-control timepicker", 'placeholder'=>"Merci d'indiquer l'heure que vous souhaitez", 'data-heure' => (string)$datas]
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Visio::class,
        ]);
    }
}

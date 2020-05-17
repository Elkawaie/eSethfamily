<?php

namespace App\Form;

use App\Entity\Ehpad;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddResidentType extends AbstractType
{
    private $ids;
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $this->ids = $options['data'][0];
        $builder
            ->add('resident', TextareaType::class,[
                "attr" => ["placeholder"=>"Merci de bien vouloir nous transmettre le nom et le prénom du resident"]
            ])
            ->add('ehpad', EntityType::class,[
                "class" => Ehpad::class,
                "choice_label" => 'nom',
                "query_builder"=> function(EntityRepository $er){
                    return $er->createQueryBuilder('e')
                        ->where('e.id IN (:ids)')
                        ->setParameter('ids', $this->ids)
                        ;
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

<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // On utilise TextType à la place de DateType pour pouvoir utiliser l'api datePiker
            // ->add('startDate', DateType::class, $this->getConfiguration("Date d'arrivée","La date à laquelle vous comptez arriver.", ["widget" => "single_text"]))
            ->add('startDate', TextType::class, $this->getConfiguration("Date d'arrivée","La date à laquelle vous comptez arriver."))
            ->add('endDate', TextType::class, $this->getConfiguration("Date de départ","La date à laquelle vous comptez partir."))
            ->add('comment', TextareaType::class, $this->getConfiguration(false,"Précisez ici vos remarques",["required" => false]))
        ;

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'validation_groups' => [
                'default',
                'front'
            ]
        ]);
    }
}

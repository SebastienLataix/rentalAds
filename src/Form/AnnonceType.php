<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AnnonceType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Titre','Titre'))
            ->add(
                'slug',
                TextType::class,
                $this->getConfiguration('Adresse web','Adresse web (automatique)',[
                    'required' => false
                ]))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Url de l\'image principale','Préciser l\'url de l\'image principale'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction','Donner une description globale de l\'annonce'))
            ->add('content', TextareaType::class, $this->getConfiguration('Description','Donner une description qui donen envie de venir chez vous !'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Nombre de chambres','Préciser le nombre de chambres disponibles'))
            ->add('price', MoneyType::class, $this->getConfiguration('Prix par nuit','Indiquer le prix demander pour une nuit'))
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}

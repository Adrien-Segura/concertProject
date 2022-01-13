<?php

namespace App\Form;

use App\Entity\Band;
use App\Entity\Concert;
use App\Entity\Picture;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BandType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom du groupe"
            ])
            ->add('picture', EntityType::class, [
                "class" => Picture::class,
                "choice_label" => "url"
            ])
            ->add('concerts', EntityType::class, [
                "class" => Concert::class,
                "choice_label" => "name",
                "multiple" => true,
                "required" => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Band::class,
        ]);
    }
}

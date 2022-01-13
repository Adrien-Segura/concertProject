<?php

namespace App\Form;

use App\Entity\Band;
use App\Entity\Concert;
use App\Entity\Member;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class MemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => "Nom du membre"
            ])
            ->add('concerts', EntityType::class, [
                "class" => Concert::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('band', EntityType::class, [
                'class' => Band::class,
                'choice_label' => 'name',
                'required' => false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Member::class,
        ]);
    }
}

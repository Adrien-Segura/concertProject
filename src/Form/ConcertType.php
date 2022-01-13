<?php

namespace App\Form;

use App\Entity\Band;
use App\Entity\Member;
use App\Entity\Room;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ConcertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateType::class, [
                'widget' => 'choice',
                'format' => 'dd / MM / yyyy'
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom du concert'
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                "choice_label" => "name"
            ])
            ->add('bands', EntityType::class, [
                'class' => Band::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('members', EntityType::class, [
                'class' => Member::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Bedroom;
use App\Entity\Booking;
use App\Repository\BedroomRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BookingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bedrooms = $options['bedrooms'];

        $builder
            ->add('customers', ChoiceType::class, [
                'choices' => [
                        1 => 1,
                        2 => 2,
                        3 => 3,
                        4 => 4,
                        5 => 5,
                        6 => 6,
                ]
            ])
            ->add('bedroomType', EntityType::class, [
                'class' => Bedroom::class,
                'choices' => $bedrooms,
                'placeholder' => 'Choose a room',
                'attr' => [
                    'onchange' => 'verif(this.value)',
                ],
            ])
            ->add('arrivalAt', DateType::class, [
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'onchange' => 'verif()'
                ],
            ])
            ->add('departureAt', DateType::class, [
                'format' => 'yyyy-MM-dd',
                'attr' => [
                    'onchange' => 'verif(this.value)'
                ],
            ])
            ->add('Reserve', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'bedrooms' => array(),
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\Bedroom;
use App\Entity\Booking;
use App\Repository\BedroomRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class BookingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bedrooms = $options['bedrooms'];

        $builder
            ->add('customers', IntegerType::class)
            ->add('bedroomType', EntityType::class, [
                'class' => Bedroom::class,
                'choices' => $bedrooms,
            ])
            ->add('arrivalAt', DateType::class)
            ->add('departureAt', DateType::class)
            ->add('Reserve', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'bedrooms' => array()
        ]);
    }
}

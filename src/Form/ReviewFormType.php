<?php

namespace App\Form;

use App\Entity\Review;
use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $bookings = $options['bookings'];
        
        $builder
            ->add('rating', RangeType::class, [
                'attr' => [
                    'min' => 0,
                    'max' => 10
                ]
            ])
            ->add('booking', EntityType::class, [
                'class' => Booking::class,
                'choices' => $bookings,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('comment', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => 'Your message should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('Send', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'bookings' => array(),
        ]);
    }
}

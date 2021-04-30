<?php

namespace App\Validator;

use App\Entity\Hotel;
use App\Repository\BookingRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class BookingValidator extends ConstraintValidator
{
    private $bookingRepo;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Booking */

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }

    public function exists($postedEntity, Hotel $hotel)
    {
        $validBookings = $this->bookingRepo->findAllArray($hotel);

        if ($validBookings){
            foreach ($validBookings as $validBooking){
                $validBooks[] = array_filter($validBooking, function ($key) {
                    return in_array($key, ['arrivalAt', 'bedroomType']);
                }, ARRAY_FILTER_USE_KEY);
            }
            
            foreach ($validBooks as $key => $validBook){
                $validBooks[$key]['arrivalAt'] = $validBook['arrivalAt']->format('Y-m-d');
            }
            return $validBooks;
        }

        return null;
    }
}

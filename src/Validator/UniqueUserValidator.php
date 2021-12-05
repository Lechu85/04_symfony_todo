<?php

namespace App\Validator;

use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueUserValidator extends ConstraintValidator
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\UniqueUser */

        if (null === $value || '' === $value) {
            return;
        }

        //NOTE ponieważ dalismy annotacje nad polem email, to $value bedzie miała wartośc właściwości email

        $existingUser = $this->userRepository->findOneBy([
            'email' => $value
        ]);

        //info jesli edytujesz to trzeba sprawdzic czy wysłany user nie jest tym samym userem, ktory był w formularzu, bo wtedy walidator zwróci błlad
        // jak nie zmienino to wtedy nie szukasz go w bazie juz.
        // ze taki juz jest a jednak nie bedzie :)

        if (!$existingUser) {
            return;
        }

        // TODO: implement the validation here
        $this->context->buildViolation($constraint->message)
            //->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}

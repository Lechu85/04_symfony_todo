<?php

namespace App\Form\DataTransformer;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class EmailToUserTransformer implements DataTransformerInterface
{
    private UserRepository $userRepository;

    //info kazdy co używa tego transformera musi podac drugi parametr callback określający jego działanie.
    private $finderCallback;

    public function __construct(UserRepository $userRepository, callable $finderCallback)
    {

        $this->userRepository = $userRepository;
        $this->finderCallback = $finderCallback;
    }

    public function transform($value)
    {

        if (null === $value) {
            return '';
        }

        if (!$value instanceof User) {
            throw new \LogicException('UserSelectTextType może być użyty tylko z obiektem User.');
        }

        //info jako reprezentacja obiektu bedzie email
        return $value->getEmail();

    }

    public function reverseTransform($value)
    {

        //info jesli nie podano wartości, to przekazujemy null do bazy co nie jest dozwolone
        // bez tego idzie pusty string
        if (!$value) {
            return;
        }

        $callback = $this->finderCallback;
        $user = $callback($this->userRepository, $value);

        //info invalid_message jest domyslnym parametrem, któemu możemy dac wartośąć.

        if (!$user) {
            throw new TransformationFailedException(sprintf(
                'Nie znaleziono użytkownika z emaiel "%s"',
                $value
            ));
        }

        return $user;
    }
}
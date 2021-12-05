<?php

namespace App\Form\Model;

use App\Validator\UniqueUser;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormModel
{

    /**
     * @Assert\NotBlank(message="Proszę podać email!")
     * @Assert\Email()
     * @UniqueUser()
     */
    public $email;

    /**
     * @Assert\NotBlank(message="Prosze podac hasło!")
     * @Assert\Length(min=5, minMessage="Twoje hasło jest zbyt krótkie!")
     */
    public $plainPassword;

    /**
     * @Assert\IsTrue(message="Akceptacja warunków serwisu jest konieczna!")
     */
    public $agreeTerms;

}
<?php

namespace App\EventDispatcher;

use App\Entity\Question;
use Symfony\Contracts\EventDispatcher\Event;

class QuestionEvent extends Event
{

    private Question $question;

    public function __construct(Question $question)
    {

        $this->question = $question;
    }

    public function getQuestion(): ?Question
    {
       return $this->question;
    }
}
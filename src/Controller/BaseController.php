<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

//info Jest to klasa abstrakcyjna, któą nigdy nioe beziemy używać,
//info ale potrzebna, żeby phpstorm robił podpowiedzi dla getUser() modułu security :)
//info naraziue używamy w AnswerController
//info BaseController można implementować w każdym kontrolerze
//info we just need to give our editor a hint so that it knows that getUser() returns our User object... not just a UserInterface.
//info w pliku AnswersController trzeba dołożyć Extends BaseContrtoller
/**
 * @method User getUser()
 */
abstract class BaseController extends AbstractController
{

}
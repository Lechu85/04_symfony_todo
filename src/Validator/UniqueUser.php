<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class UniqueUser extends Constraint
{

    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Klient z takim emailem już istnieje w bazie.';

    //NOTE obiekt konfiguracji zapory, klase tą użyjemy w annotacjach innej klasy
}

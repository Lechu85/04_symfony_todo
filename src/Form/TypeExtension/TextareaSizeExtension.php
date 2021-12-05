<?php

namespace App\Form\TypeExtension;

//info w nowszym symfony po 4,2 nie dodajemy jużdenfinicji w symfony.yaml
//info możemy tutaj swoje parametry wymnyslac, a w klasie formularza tylko te zdefiniowane
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeExtensionInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextareaSizeExtension implements FormTypeExtensionInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['rows'] = $options['rows'];
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        //info każdy textarea ma teraz 8 wierszy w serwisie.
        // w tej metodzie mozemy definiować nowe opcje, któe użyć możemy w danej klasie formularza w danym typie pola
        $resolver->setDefaults([
            'rows' => 8
        ]);
    }

    //info Dzieki tej metodzie symfony wie o takim rozszerzeniu i nie trzeba zgłaszać w servces.yaml
    // w niektórych wersjach symfony dalej potrzebujesz pubf getExtendedTypes() { return''; }
    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class];
        //info FormType zmienia każd jedno pole

    }
}

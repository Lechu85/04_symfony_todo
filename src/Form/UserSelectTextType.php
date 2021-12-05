<?php

namespace App\Form;

use App\Form\DataTransformer\EmailToUserTransformer;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;


class UserSelectTextType extends AbstractType
{
    private UserRepository $userRepository;
    private RouterInterface $router;

    public function __construct(UserRepository $userRepository, RouterInterface $router)
    {
        $this->userRepository = $userRepository;
        $this->router = $router;
    }

    //info $options bedzie teraz zawierał funkcje callback
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //info callback to drugi parametr, któy będziemy wstawiać w articleformtype
        // opcja chyba dobrze nie zadziałąła trzeba skoiować trećź w plików symfonycasts forms.
        $builder->addModelTransformer(new EmailToUserTransformer(
            $this->userRepository,
            $options['finder_callback']
        ));
    }

    public function getParent()
    {
        return TextType::class;
    }

    //info tutaj dsą domyślne opcje dla tego pola, to sam oco w klasie formularza przydanym polu jak podajesz opcje
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'Nie znaleziono użytkownika',
            //info finder_callback to parametr utworzony przez nas w data transformerze
            'finder_callback' => function(UserRepository $userRepository, string $email) {
                return $userRepository->findOneBy(['email' => $email]);
            },
            'attr' => [
                'class' => 'js-user-autocomplete',
                //info wskakuje nowy atrybut do tego inputa
                'data-autocomplete-url' => $this->router->generate('admin_utility_users'),
            ]
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /*
        //info metoda ta jesy wywoływana przy każdym polu
        $attr = $view->vars['attr'];
        //info tutaj sprawdzamy czy nie nadpisujemy parametru któy podaliśmy indywidualnie przy polu.
        $class = isset($attr['class']) ? $attr['class'].' ' : '';
        $class .= 'js-user-autocomplete';

        //info chyba uzupelniamy tuaj klase ogolna z klasą któą podalismy, i sumujemy je
        $attr['class'] = $class;
        $attr['data-autocomplete-url'] = $this->router->generate('admin_utility_users');

        $view->vars['attr'] = $attr;
*/
        //info nie ważne co dalismy w polu opcji bedzie zsumowane
    }


}

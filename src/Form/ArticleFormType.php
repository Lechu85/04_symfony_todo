<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Clue\StreamFilter\remove;
use function Sodium\add;

class ArticleFormType extends AbstractType
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //info metoda w której budujemy pola
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //info jeżeli edytujemy formularz to klucz 'data' zwróci nam obiekt
        /** @var Article|null $article */
        $article = $options['data'] ?? null;
        $isEdit = $article && $article->getId();//info jesli edycja
//        $location = $article ? $article->getLocation() : null;//NOTE if article is object wtedy weź lokalizacje, w innym przypadku null.

        $builder
            ->add('title', TextType::class, [
                'help' => 'Wpisz coś ciekawego'
            ])
            ->add('content', null, [ //jak jest null to zgaduje
               // 'rows' => 15 //podano dla całej strony, można nadpisywać
            ])
            ->add('author', UserSelectTextType::class, [
                'disabled' => $isEdit
            ])
            ->add('location', ChoiceType::class, [
                'placeholder' => 'Wybierz lokacje',
                'choices' => [
                    'The solar system' => 'solar_system',
                    'Near a star' => 'star',
                    'Interstellar space' => 'interstellar_space',
                ],
                'required' => false
            ])
        ;

//        //NOTE if location is set add that builder
//        if ($location) {
//            $builder->add('specificLocationName', ChoiceType::class, [
//                'placeholder' => 'Gdzie dokładnie?',
//                'choices' => $this->getLocationNameChoices($location),
//                'required' => false
//            ]);
//        }

        if ($options['include_published_at']) {
            $builder->add('publishedAt', DateTimeType::class, [
                'widget' => 'single_text'
            ]);
        }

        //NOTE przypiosujkemy event do całego formularza - ogladnac jezc ze chapter 38 bo juz nie miałe msiły :)
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var Article|null $data */
                $data = $event->getData();

                //NOTE jesli nie ma danych to zwracamy nic
                if (!$data) {
                    return;
                }

                //NOTE Jeśli jest $data to robimy callup:
                $this->setupSpecificLocationNameField(
                    $event->getForm(),
                    $data->getLocation()
                );

            }

        );

        $builder->get('location')->addEventListener(
            FormEvents::POST_SUBMIT,
            function(FormEvent $event) {
                //NOTE tutaj pobieramy obiekt formularza dla tegop pola
                // obiekt, któy reprezentuje tylko ppole location
                $form = $event->getForm();
                $this->setupSpecificLocationNameField(
                    $form->getParent(),
                    $form->getData()
                );
            }
        );

    }

    //tutja jest konfiguracja dla wszystkicxh pól formularza
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'include_published_at' => false,
        ]);

    }

    //NOTE lista pól do wyboru wstawina na sztywno
    private function getLocationNameChoices(string $location)
    {
        $planets = [
            'Mercury',
            'Venus',
            'Earth',
            'Mars',
            'Jupiter',
            'Saturn',
            'Uranus',
            'Neptune',
        ];

        $stars = [
            'Polaris',
            'Sirius',
            'Alpha Centauari A',
            'Alpha Centauari B',
            'Betelgeuse',
            'Rigel',
            'Other'
        ];

        $locationNameChoices = [
            'solar_system' => array_combine($planets, $planets),
            'star' => array_combine($stars, $stars),
            'interstellar_space' => null,
        ];

        //NOTE w przypadku gdy w selekcie wybierzemy pustte pole, placeholder. wtedy zwraca ttuaj null.
        return $locationNameChoices[$location] ?? null;
    }

    public function setupSpecificLocationNameField(FormInterface $form, ?string $location)
    {
        if(null === $location) {
            $form->remove('specificLocationName');

            return;
        }

        $choices = $this->getLocationNameChoices($location);

        //NOTE jezeli nie wybrano w pierwszym polu to pole drugie jest kasowane i nie uaktualnia się.
        // więc jeżeli chcemy w polu dac wartośc "" albo interstellar to trzeba w encji Article w setLocation() dodac, że jak nie podano, to zerujemy
        if (null === $choices) {
            $form->remove('specificLocationName');

            return;
        }

        $form->add('specificLocationName', ChoiceType::class, [
            'placeholder' => 'Gdzie dokładnie?',
            'choices' => $choices,
            'required' => false
        ]);
        
    }

}
<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
        $article = $options['data'] ?? null;
        $isEdit = $article && $article->getId();//info jesli edycja

        $builder
            ->add('title', TextType::class, [
                'help' => 'Wpisz coś ciekawego'
            ])
            ->add('content', null, [ //jak jest null to zgaduje
                'rows' => 15
            ])
            ->add('author', UserSelectTextType::class, [
                'disabled' => $isEdit
            ]);
        ;

        if ($options['include_published_at']) {
            $builder->add('publishedAt', DateTimeType::class, [
                'widget' => 'single_text'
            ]);
        }

    }

    //tutja jest konfiguracja dla wszystkicxh pól formularza
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'include_published_at' => false,
        ]);

    }

}
<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
//info Zdarzenie jest emitowane tuż przed wykonaniem kodu kontrolera.
//info Jest to najlepszy moment na wstrzyknięcie globalnej zmiennej cms_menu w taki sposób,
//info aby Twig miał do niej dostęp, kiedy kontroler będzie renderował szablon.
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{

    private Environment $twig;
    private $cms_menu;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
        $this->cms_menu = ['admin_article_list' => 'Artykuły', 'admin_article_new' => 'Dodaj artykuł'];

    }
    public function onControllerEvent(ControllerEvent $event)
    {
        //info Teraz możesz dodać dowolną liczbę kontrolerów: zmienna conferences będzie zawsze dostępna w szablonach Twig.
        $this->twig->addGlobal('cms_menu', $this->cms_menu);
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}

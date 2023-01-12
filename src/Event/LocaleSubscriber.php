<?php

namespace App\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultlocale;

    public function __construct(string $defaultlocale)
    {
        $this->defaultlocale = $defaultlocale;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()){
            return;
        }

        $locale = $request->attributes->get('_locale');

        if($locale) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale(
                $request->getSession()->get('_locale', $this->defaultlocale)
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST =>[
                [
                    'onKernelRequest', 20
                ]
            ]
        ];
    }
}
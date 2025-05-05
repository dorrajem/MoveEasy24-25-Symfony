<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $router;
    private $defaultLocale;

    public function __construct(RouterInterface $router, string $defaultLocale = 'fr')
    {
        $this->router = $router;
        $this->defaultLocale = $defaultLocale;
    }
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->get('_locale')) {
            $request->setLocale('fr');
        }
    }
    /*public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // If the route already has _locale, do nothing
        if ($request->attributes->get('_locale')) {
            return;
        }

        // Detect browser language
        $preferredLanguage = $request->getPreferredLanguage(['fr', 'en']) ?? $this->defaultLocale;

        // Redirect to homepage with detected locale
        $url = $this->router->generate('app_client', ['_locale' => $preferredLanguage]);
        $event->setResponse(new RedirectResponse($url));
    }*/

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
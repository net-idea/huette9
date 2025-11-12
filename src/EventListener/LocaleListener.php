<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener implements EventSubscriberInterface
{
    private string $defaultLocale;
    private array $supportedLocales;

    public function __construct(string $defaultLocale = 'en', array $supportedLocales = ['en', 'de'])
    {
        $this->defaultLocale = $defaultLocale;
        $this->supportedLocales = $supportedLocales;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Try to get locale from session first
        if ($locale = $request->getSession()->get('_locale')) {
            $request->setLocale($locale);
            return;
        }

        // Try to get locale from route parameter
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
            $request->getSession()->set('_locale', $locale);
            return;
        }

        // Detect from browser Accept-Language header
        $preferredLanguage = $request->getPreferredLanguage($this->supportedLocales);

        if ($preferredLanguage) {
            $request->setLocale($preferredLanguage);
            $request->getSession()->set('_locale', $preferredLanguage);
        } else {
            $request->setLocale($this->defaultLocale);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 120)]
class LocaleListener
{
    private const SUPPORTED_LOCALES = ['de', 'en'];
    private const DEFAULT_LOCALE = 'de';

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        // Skip if not the main request
        if (!$event->isMainRequest()) {
            return;
        }

        // 1) Session hat Vorrang, wenn vorhanden
        if ($request->hasPreviousSession()) {
            $session = $request->getSession();
            if ($session->has('_locale')) {
                $locale = (string) $session->get('_locale');
                $request->setLocale($locale);

                return;
            }
        }

        // 2) Browser Accept-Language auswerten
        $preferred = $request->getPreferredLanguage(self::SUPPORTED_LOCALES);

        if (null !== $preferred) {
            // Nur Sprachcode verwenden (de-DE => de)
            $locale = substr($preferred, 0, 2);

            if (!\in_array($locale, self::SUPPORTED_LOCALES, true)) {
                $locale = self::DEFAULT_LOCALE;
            }
        } else {
            $locale = self::DEFAULT_LOCALE;
        }

        $request->setLocale($locale);
    }
}

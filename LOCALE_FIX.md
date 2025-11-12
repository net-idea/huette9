# ✅ Locale-Problem behoben

## Problem
Das Booking-Formular wurde immer auf Englisch angezeigt, auch wenn die deutsche Locale ausgewählt war.

## Ursache
Die Locale wurde zwar in der Session gespeichert (`_locale`), aber Symfony hat sie nicht automatisch auf den Request angewendet. Das führte dazu, dass:
1. Die Template-Anzeige (`app.request.locale`) korrekt war
2. Aber das Formular mit der Default-Locale (en) erstellt wurde
3. Die Formular-Labels waren daher immer auf Englisch

## Lösung
Erstellt einen `LocaleListener` EventListener, der:
- Bei jedem Request (mit Priorität 20) ausgeführt wird
- Die Locale aus der Session lädt (`_locale`)
- Die Locale auf den Request setzt (`$request->setLocale()`)
- VOR dem Symfony LocaleListener läuft, damit die Locale rechtzeitig gesetzt wird

## Implementierung

### Neue Datei
- `src/EventListener/LocaleListener.php`

### EventListener-Reihenfolge
```
#6  Symfony LocaleListener::setDefaultLocale()     Priority: 100
#8  RouterListener::onKernelRequest()              Priority: 32
#9  App\EventListener\LocaleListener::__invoke()   Priority: 20  ← NEU
#10 Symfony LocaleListener::onKernelRequest()      Priority: 16
```

Der neue Listener läuft mit Priorität 20, also:
- NACH dem Router (32) - Request ist initialisiert
- VOR dem Symfony LocaleListener (16) - Locale wird rechtzeitig gesetzt

## Testen

1. Öffne `/booking` - sollte auf Englisch sein (Default)
2. Wechsle zu Deutsch über das Sprach-Menü
3. Navigiere zu `/booking` - sollte jetzt auf Deutsch sein
4. Labels und Platzhalter sollten übersetzt sein
5. Das gleiche gilt für `/contact`

## Technische Details

### Wie funktioniert es?

1. **LocaleController** speichert Locale in Session:
   ```php
   $request->getSession()->set('_locale', $locale);
   ```

2. **LocaleListener** lädt Locale aus Session:
   ```php
   if ($session->has('_locale')) {
       $locale = $session->get('_locale');
       $request->setLocale($locale);
   }
   ```

3. **FormType** nutzt Request-Locale für Labels:
   - Symfony FormType nutzt automatisch `$request->getLocale()`
   - Labels werden mit dem Translator übersetzt
   - Platzhalter und Validierungen nutzen die korrekte Sprache

## Status
✅ LocaleListener erstellt
✅ In Event-Dispatcher registriert (automatisch via #[AsEventListener])
✅ Cache geleert
✅ Keine PHP-Fehler

## Verwandte Dateien
- `src/Controller/LocaleController.php` - Speichert Locale in Session
- `src/EventListener/LocaleListener.php` - Lädt Locale aus Session
- `config/packages/translation.yaml` - Default Locale: en
- `translations/messages.*.yaml` - Übersetzungen

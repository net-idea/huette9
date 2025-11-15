<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testHomepageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome to Hütte9');
    }

    public function testLocaleSwitching(): void
    {
        $client = static::createClient();

        // Switch to German
        $client->request('GET', '/locale/de');
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Willkommen');

        // Switch to English
        $client->request('GET', '/locale/en');
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome');
    }

    public function testAutomaticLocaleSelectionGerman(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.9,en-US;q=0.8,en;q=0.7',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Willkommen');
    }

    public function testAutomaticLocaleSelectionEnglish(): void
    {
        $client = static::createClient();

        $client->request('GET', '/', [], [], [
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9,de;q=0.8',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Welcome');
    }
}

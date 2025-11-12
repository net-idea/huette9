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

    public function testContactPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Get in Touch');
    }

    public function testContactFormIsPresent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();

        // Check form fields are present
        $this->assertCount(1, $crawler->filter('input[name="contact[name]"]'));
        $this->assertCount(1, $crawler->filter('input[name="contact[email]"]'));
        $this->assertCount(1, $crawler->filter('input[name="contact[subject]"]'));
        $this->assertCount(1, $crawler->filter('textarea[name="contact[message]"]'));
    }

    public function testLocaleSwitching(): void
    {
        $client = static::createClient();

        // Test switching to German
        $client->request('GET', '/locale/de');
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Verify German content
        $this->assertSelectorTextContains('h1', 'Willkommen');

        // Test switching to English
        $client->request('GET', '/locale/en');
        $this->assertResponseRedirects();

        $crawler = $client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Verify English content
        $this->assertSelectorTextContains('h1', 'Welcome');
    }
}

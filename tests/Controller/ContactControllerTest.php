<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testContactPageIsSuccessful(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        // Ensure a contact form is present by class instead of exact Symfony form name
        $this->assertSelectorExists('form.contact-form');
    }

    public function testContactFormIsPresent(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();

        // Basic structure: at least one text input, one email input, and one textarea
        $this->assertGreaterThan(0, $crawler->filter('form.contact-form input[type="text"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('form.contact-form input[type="email"]')->count());
        $this->assertGreaterThan(0, $crawler->filter('form.contact-form textarea')->count());
    }
}

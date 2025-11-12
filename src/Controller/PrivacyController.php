<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class PrivacyController extends AbstractController
{
    #[Route('/datenschutz', name: 'app_datenschutz')]
    #[Route('/privacy', name: 'app_privacy')]
    public function index(
        Request $request,
        Environment $twig
    ): Response {
        // Select template based on locale
        $locale = $request->getLocale();

        // Use datenschutz for German, privacy for English
        $templateName = 'de' === $locale ? 'datenschutz' : 'privacy';
        $template = sprintf('pages/%s.%s.html.twig', $templateName, $locale);

        // Fallback to English privacy if locale template doesn't exist
        if (!$twig->getLoader()->exists($template)) {
            $template = 'pages/privacy.en.html.twig';
        }

        return $this->render($template);
    }
}

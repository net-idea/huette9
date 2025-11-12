<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class ImprintController extends AbstractController
{
    #[Route('/impressum', name: 'app_impressum')]
    #[Route('/imprint', name: 'app_imprint')]
    public function index(
        Request $request,
        Environment $twig
    ): Response {
        // Select template based on locale
        $locale = $request->getLocale();

        // Use impressum for German, imprint for English
        $templateName = 'de' === $locale ? 'impressum' : 'imprint';
        $template = sprintf('pages/%s.%s.html.twig', $templateName, $locale);

        // Fallback to English imprint if locale template doesn't exist
        if (!$twig->getLoader()->exists($template)) {
            $template = 'pages/imprint.en.html.twig';
        }

        return $this->render($template);
    }
}

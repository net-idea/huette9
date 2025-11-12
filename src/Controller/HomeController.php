<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, Environment $twig): Response
    {
        $locale = $request->getLocale();
        $template = sprintf('pages/home.%s.html.twig', $locale);

        // Fallback to English if locale template doesn't exist
        if (!$twig->getLoader()->exists($template)) {
            $template = 'pages/home.en.html.twig';
        }

        return $this->render($template);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\FormContactService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        FormContactService $contactService,
        TranslatorInterface $translator,
        Environment $twig
    ): Response {
        $form = $contactService->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $redirect = $contactService->handle();

            if ($redirect) {
                // Check for success or error in query params
                if ('1' === $request->query->get('submit')) {
                    $this->addFlash('success', $translator->trans('contact.success'));
                } elseif ('mail' === $request->query->get('error')) {
                    $this->addFlash('error', $translator->trans('error.mail_send_failed'));
                } elseif ('rate' === $request->query->get('error')) {
                    $this->addFlash('error', $translator->trans('error.rate_limit'));
                }

                return $redirect;
            }
        }

        // Select template based on locale
        $locale = $request->getLocale();
        $template = sprintf('pages/contact.%s.html.twig', $locale);

        // Fallback to English if locale template doesn't exist
        if (!$twig->getLoader()->exists($template)) {
            $template = 'pages/contact.en.html.twig';
        }

        return $this->render($template, [
            'form' => $form,
        ]);
    }
}

<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\FormBookingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class BookingController extends AbstractController
{
    #[Route('/booking', name: 'app_booking')]
    public function index(
        Request $request,
        FormBookingService $bookingService,
        TranslatorInterface $translator,
        Environment $twig
    ): Response {
        $form = $bookingService->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $redirect = $bookingService->handle();
            if ($redirect) {
                // Check for success or error in query params
                if ('1' === $request->query->get('submit')) {
                    $this->addFlash('success', $translator->trans('booking.success'));
                } elseif ('mail' === $request->query->get('error')) {
                    $this->addFlash('error', $translator->trans('error.mail_send_failed'));
                } elseif ('db' === $request->query->get('error')) {
                    $this->addFlash('error', $translator->trans('error.database_error'));
                } elseif ('rate' === $request->query->get('error')) {
                    $this->addFlash('error', $translator->trans('error.rate_limit'));
                }

                return $redirect;
            }
        }

        // Select template based on locale
        $locale = $request->getLocale();
        $template = sprintf('pages/booking.%s.html.twig', $locale);

        // Fallback to English if locale template doesn't exist
        if (!$twig->getLoader()->exists($template)) {
            $template = 'pages/booking.en.html.twig';
        }

        return $this->render($template, [
            'form' => $form,
        ]);
    }

    #[Route('/booking/confirm/{token}', name: 'app_booking_confirm')]
    public function confirm(
        Request $request,
        FormBookingService $bookingService,
        TranslatorInterface $translator,
        Environment $twig,
        string $token
    ): Response {
        $status = $bookingService->confirmBooking($token);

        // Select template based on locale
        $locale = $request->getLocale();
        $template = sprintf('pages/booking-confirm.%s.html.twig', $locale);

        // Fallback to English if locale template doesn't exist
        if (!$twig->getLoader()->exists($template)) {
            $template = 'pages/booking-confirm.en.html.twig';
        }

        return $this->render($template, [
            'status'        => $status,
            'statusMessage' => $translator->trans('booking.confirm.' . $status),
        ]);
    }
}

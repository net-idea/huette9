<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\FormBookingEntity;
use App\Entity\FormContactEntity;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Twig\Environment as Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class MailManService
{
    public function __construct(
        private MailerInterface $mailer,
        private Twig $twig,
        private string $fromAddress,
        private string $fromName,
        private string $toAddress,
        private string $toName,
        private LoggerInterface $logger,
        private RequestStack $requestStack
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     * @throws SyntaxError
     */
    public function sendContactForm(FormContactEntity $contact): void
    {
        $from = new Address($this->fromAddress, $this->fromName);
        $to = new Address($this->toAddress, $this->toName);

        $context = ['contact' => $contact];

        // Determine locale from current request (default to 'de')
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'de';

        try {
            $ownerSubject = 'Hütte9 — Neue Kontaktanfrage';
            $ownerText = $this->twig->render("email/contact_owner.{$locale}.txt.twig", $context);
            $ownerHtml = $this->twig->render("email/contact_owner.{$locale}.html.twig", $context);

            $emailOwner = (new Email())
                ->from($from)
                ->to($to)
                ->replyTo(new Address($contact->getEmailAddress(), $contact->getName()))
                ->subject($ownerSubject)
                ->text($ownerText)
                ->html($ownerHtml);

            $this->mailer->send($emailOwner);
            $this->logger->info('Contact mail sent to owner', [
                'to'    => $to->getAddress(),
                'name'  => $to->getName(),
                'email' => $contact->getEmailAddress(),
            ]);

            if ($contact->getCopy()) {
                $visitorSubject = 'de' === $locale ? 'Hütte9 — Ihre Kontaktanfrage' : 'Hütte9 — Your Contact Request';
                $visitorText = $this->twig->render("email/contact_visitor.{$locale}.txt.twig", $context);
                $visitorHtml = $this->twig->render("email/contact_visitor.{$locale}.html.twig", $context);

                $emailVisitor = (new Email())
                    ->from($from)
                    ->to(new Address($contact->getEmailAddress(), $contact->getName()))
                    ->subject($visitorSubject)
                    ->text($visitorText)
                    ->html($visitorHtml);

                $this->mailer->send($emailVisitor);
                $this->logger->info('Contact mail sent to visitor', [
                    'to'   => $contact->getEmailAddress(),
                    'name' => $contact->getName(),
                ]);
            }
        } catch (TransportExceptionInterface $e) {
            // Logs transport failures (bad DSN, auth, SSL, DNS, etc.)
            $this->logger->error('Mailer send failed: ' . $e->getMessage(), ['exception' => $e]);

            throw $e;
        }
    }

    /**
     * Send a confirmation request to the visitor with a unique link.
     * @throws TransportExceptionInterface|RuntimeError|LoaderError|SyntaxError
     */
    public function sendBookingVisitorConfirmationRequest(FormBookingEntity $booking, string $confirmUrl): void
    {
        $from = new Address($this->fromAddress, $this->fromName);
        $toVisitor = new Address($booking->getContactEmail(), $booking->getContactName());

        $context = [
            'booking'    => $booking,
            'confirmUrl' => $confirmUrl,
        ];

        // Determine locale from current request (default to 'de')
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'de';

        // Log before attempting to render or send
        $this->logger->info(
            'Preparing booking confirmation request',
            [
                'to'     => $toVisitor->getAddress(),
                'name'   => $toVisitor->getName(),
                'token'  => substr($booking->getConfirmationToken(), 0, 6) . '…',
                'locale' => $locale,
            ]
        );

        try {
            $subject = 'de' === $locale
                ? 'Hütte9 — Bitte bestätigen Sie Ihre Buchung'
                : 'Hütte9 — Please Confirm Your Booking';
            $text = $this->twig->render("email/booking_visitor_confirm_request.{$locale}.txt.twig", $context);
            $html = $this->twig->render("email/booking_visitor_confirm_request.{$locale}.html.twig", $context);

            $email = (new Email())
                ->from($from)
                ->to($toVisitor)
                ->replyTo(new Address($this->toAddress, $this->toName))
                ->subject($subject)
                ->text($text)
                ->html($html);

            $this->mailer->send($email);
            $this->logger->info('Booking confirmation request sent successfully', [
                'to'        => $toVisitor->getAddress(),
                'bookingId' => $booking->getId(),
            ]);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Mailer transport failed', [
                'exception' => $e->getMessage(),
                'to'        => $toVisitor->getAddress(),
                'bookingId' => $booking->getId(),
            ]);

            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('Email preparation or sending failed', [
                'exception' => $e->getMessage(),
                'to'        => $toVisitor->getAddress(),
                'bookingId' => $booking->getId(),
            ]);

            throw $e;
        }
    }

    /**
     * Notify the owner when a booking was confirmed by the visitor.
     * @throws TransportExceptionInterface|RuntimeError|LoaderError|SyntaxError
     */
    public function sendBookingOwnerNotification(FormBookingEntity $booking): void
    {
        $from = new Address($this->fromAddress, $this->fromName);
        $toOwner = new Address($this->toAddress, $this->toName);

        $context = ['booking' => $booking];

        // Determine locale from current request (default to 'de')
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'de';

        $subject = 'de' === $locale
            ? 'Hütte9 — Buchung bestätigt'
            : 'Hütte9 — Booking Confirmed';
        $text = $this->twig->render("email/booking_owner_confirmed.{$locale}.txt.twig", $context);
        $html = $this->twig->render("email/booking_owner_confirmed.{$locale}.html.twig", $context);

        $email = (new Email())
            ->from($from)
            ->to($toOwner)
            ->replyTo(new Address($booking->getContactEmail(), $booking->getContactName()))
            ->subject($subject)
            ->text($text)
            ->html($html);

        $this->logger->info('Sending booking owner notification', [
            'to'        => $toOwner->getAddress(),
            'name'      => $toOwner->getName(),
            'bookingId' => $booking->getId(),
        ]);

        try {
            $this->mailer->send($email);
            $this->logger->info('Booking notification sent to owner');
        } catch (TransportExceptionInterface $e) {
            // Logs transport failures (bad DSN, auth, SSL, DNS, etc.)
            $this->logger->error('Mailer send failed: ' . $e->getMessage(), ['exception' => $e]);

            throw $e;
        }
    }
}

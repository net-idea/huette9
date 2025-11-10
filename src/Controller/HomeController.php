<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(Request $request, EntityManagerInterface $entityManager, RateLimiterFactory $contactFormLimiter): Response
    {
        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Rate limiting - spam protection
            $limiter = $contactFormLimiter->create($request->getClientIp());
            if (false === $limiter->consume(1)->isAccepted()) {
                throw new TooManyRequestsHttpException(
                    null,
                    'Zu viele Anfragen. Bitte versuchen Sie es später erneut.'
                );
            }
            
            $entityManager->persist($contact);
            $entityManager->flush();
            
            $this->addFlash('success', 'Vielen Dank für Ihre Nachricht! Wir werden uns bald bei Ihnen melden.');
            
            return $this->redirectToRoute('app_home');
        }
        
        return $this->render('home/index.html.twig', [
            'form' => $form,
        ]);
    }
}

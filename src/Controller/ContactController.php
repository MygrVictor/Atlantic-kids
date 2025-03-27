<?php

namespace App\Controller;

use App\Form\ContactForm;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();  // Données du formulaire

            // Créer l'email
            $email = (new Email())
                ->from($data['email'])
                ->to('b4e56393a50d15@sandbox.smtp.mailtrap.io:2525') // Adresse où recevoir l'email
                ->subject('Nouveau message de contact')
                ->text(
                    "Nom: " . $data['name'] . "\n\n" .
                    "Email: " . $data['email'] . "\n\n" .
                    "Message:\n" . $data['message']
                );

            // Envoyer l'email
            $mailer->send($email);

            // Message de succès pour l'utilisateur
            $this->addFlash('success', 'Message envoyé avec succès !');

            // Rediriger ou afficher un message de confirmation
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager
    ) {
    }
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        // Générer la signature pour l'email de confirmation
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );
    
        // Récupérer le contexte existant de l'email
        $context = $email->getContext();
    
        // Ajouter les données nécessaires au contexte
        $context['user'] = $user; // Assurez-vous que 'user' est bien passé
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();
        
        // Si vous avez besoin d'une clé 'expiresAt', vous pouvez la définir explicitement
        // Exemple : Si vous souhaitez afficher une date d'expiration
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval('PT24H')); // Date d'expiration dans 24h
        $context['expiresAt'] = $expiresAt->format('Y-m-d H:i:s'); // Formater la date
    
        // Mettre à jour le contexte de l'email
        $email->context($context);
    
        // Envoyer l'email
        $this->mailer->send($email);
    }
    
}    
    
<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class UserAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
   

    public function __construct(private UrlGeneratorInterface $urlGenerator)
    {
    }

    public function supports(Request $request): bool
    {
        // Supporte les requêtes de la route de connexion (POST) et la route de création de vidéo (POST)
        return (
            ($request->attributes->get('_route') === self::LOGIN_ROUTE && $request->isMethod('POST')) 
           
        );
    }

    public function authenticate(Request $request): Passport
{
    $email = $request->request->get('email');
    
    // Vérifie si l'email est bien défini
    if (empty($email)) {
        throw new AuthenticationException('Email is required.');
    }

    $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

    return new Passport(
        new UserBadge($email), // Le paramètre attendu ici doit être une chaîne (email)
        new PasswordCredentials($request->request->get('password')),
        [
            new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            new RememberMeBadge(),
        ]
    );
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Redirection vers la page précédente si elle existe, sinon vers la page d'accueil
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_user'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        // Message flash en cas d'échec d'authentification
        $request->getSession()->getFlashBag()->add('error', 'Invalid credentials.');

        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }
}

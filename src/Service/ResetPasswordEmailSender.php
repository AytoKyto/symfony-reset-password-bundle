<?php

namespace Ayto\ResetPasswordBundle\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class ResetPasswordEmailSender
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator,
        private string $fromEmail,
        private int $tokenLifetime
    ) {
    }

    public function sendResetPasswordEmail(string $toEmail, string $token): void
    {
        $resetUrl = $this->urlGenerator->generate('reset_password_reset', [
            'token' => $token
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $html = $this->twig->render('@ResetPassword/emails/reset_password.html.twig', [
            'resetUrl' => $resetUrl,
            'tokenLifetime' => $this->tokenLifetime
        ]);

        $email = (new Email())
            ->from($this->fromEmail)
            ->to($toEmail)
            ->subject('Réinitialisation de votre mot de passe')
            ->html($html);

        $this->mailer->send($email);
    }
}

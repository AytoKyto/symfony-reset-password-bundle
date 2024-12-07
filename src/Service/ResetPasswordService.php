<?php

namespace Ayto\ResetPasswordBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Ayto\ResetPasswordBundle\Entity\ResetPasswordRequest;
use Ayto\ResetPasswordBundle\Model\ResetPasswordUserInterface;
use DateTime;
use DateInterval;
use Exception;

class ResetPasswordService
{
    private const TOKEN_LIFETIME = 'PT1H'; // 1 hour

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailerInterface $mailer,
        private string $frontendResetUrl,
        private string $userClass
    ) {
    }

    public function generateResetToken(string $userEmail): ?string
    {
        $user = $this->entityManager->getRepository($this->userClass)->findOneBy(['email' => $userEmail]);
        
        if (!$user instanceof ResetPasswordUserInterface) {
            return null;
        }

        // Generate unique token
        $selector = bin2hex(random_bytes(20));
        $verifier = bin2hex(random_bytes(20));
        
        // Hash the verifier for storage
        $hashedToken = password_hash($verifier, PASSWORD_DEFAULT);
        
        // Calculate expiration
        $expiresAt = (new DateTime('now'))->add(new DateInterval(self::TOKEN_LIFETIME));
        
        // Create and save reset request
        $resetRequest = new ResetPasswordRequest(
            $user,
            $expiresAt,
            $selector,
            $hashedToken
        );
        
        // Remove any existing reset requests for this user
        $this->removeExistingResetRequests($userEmail);
        
        $this->entityManager->persist($resetRequest);
        $this->entityManager->flush();
        
        return $selector . ':' . $verifier;
    }

    public function sendResetEmail(string $userEmail, string $token): void
    {
        $resetUrl = $this->frontendResetUrl . '?token=' . $token;
        
        $email = (new Email())
            ->from('no-reply@yourdomain.com')
            ->to($userEmail)
            ->subject('Réinitialisation de votre mot de passe')
            ->html(sprintf(
                'Cliquez sur ce lien pour réinitialiser votre mot de passe : <a href="%s">Réinitialiser le mot de passe</a><br>
                Ce lien expirera dans 1 heure.',
                $resetUrl
            ));
        
        $this->mailer->send($email);
    }

    public function validateTokenAndFetchUser(string $fullToken): ?ResetPasswordUserInterface
    {
        [$selector, $verifier] = explode(':', $fullToken);
        
        $resetRequest = $this->entityManager->getRepository(ResetPasswordRequest::class)
            ->findOneBy(['selector' => $selector]);
            
        if (!$resetRequest || $resetRequest->isExpired()) {
            return null;
        }
        
        if (!password_verify($verifier, $resetRequest->getHashedToken())) {
            return null;
        }
        
        return $resetRequest->getUser();
    }

    private function removeExistingResetRequests(string $userEmail): void
    {
        $existingRequests = $this->entityManager->getRepository(ResetPasswordRequest::class)
            ->findBy(['userEmail' => $userEmail]);
            
        foreach ($existingRequests as $request) {
            $this->entityManager->remove($request);
        }
        
        $this->entityManager->flush();
    }
}

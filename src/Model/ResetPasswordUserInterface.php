<?php

namespace Ayto\ResetPasswordBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface ResetPasswordUserInterface extends UserInterface
{
    /**
     * Retourne l'email de l'utilisateur
     */
    public function getEmail(): string;
    
    /**
     * Retourne le token de réinitialisation de mot de passe
     */
    public function getResetPasswordToken(): ?string;
    
    /**
     * Définit le token de réinitialisation de mot de passe
     */
    public function setResetPasswordToken(?string $token): void;
    
    /**
     * Retourne la date d'expiration du token
     */
    public function getResetPasswordTokenExpiresAt(): ?\DateTimeInterface;
    
    /**
     * Définit la date d'expiration du token
     */
    public function setResetPasswordTokenExpiresAt(?\DateTimeInterface $expiresAt): void;
    
    /**
     * Met à jour le mot de passe de l'utilisateur
     */
    public function setPassword(string $password): void;
}

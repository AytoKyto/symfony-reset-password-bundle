<?php

namespace Ayto\ResetPasswordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use DateTime;
use Ayto\ResetPasswordBundle\Model\ResetPasswordUserInterface;

#[ORM\Entity]
#[ORM\MappedSuperclass]
class ResetPasswordRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ResetPasswordUserInterface::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ResetPasswordUserInterface $user;

    #[ORM\Column(type: 'string', length: 100)]
    private string $selector;

    #[ORM\Column(type: 'string', length: 100)]
    private string $hashedToken;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $requestedAt;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $expiresAt;

    #[ORM\Column(type: 'string', length: 255)]
    private string $userEmail;

    public function __construct(ResetPasswordUserInterface $user, DateTimeInterface $expiresAt, string $selector, string $hashedToken)
    {
        $this->user = $user;
        $this->userEmail = $user->getEmail();
        $this->requestedAt = new DateTime('now');
        $this->expiresAt = $expiresAt;
        $this->selector = $selector;
        $this->hashedToken = $hashedToken;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getHashedToken(): string
    {
        return $this->hashedToken;
    }

    public function getRequestedAt(): DateTimeInterface
    {
        return $this->requestedAt;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getUser(): ResetPasswordUserInterface
    {
        return $this->user;
    }

    public function isExpired(): bool
    {
        return $this->expiresAt->getTimestamp() <= time();
    }
}

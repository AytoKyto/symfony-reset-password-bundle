<?php

namespace Ayto\ResetPasswordBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Ayto\ResetPasswordBundle\Controller\ResetPasswordRequestController;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'ResetPasswordRequest',
    operations: [
        new Post(
            uriTemplate: '/reset-password/request',
            formats: ['json' => ['application/json'], 'jsonld' => ['application/ld+json']],
            status: 202,
            processor: ResetPasswordRequestController::class
        ),
    ],
    formats: ['json']
)]
class ResetPasswordRequest
{
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email n'est pas valide")]
    public string $email;
}

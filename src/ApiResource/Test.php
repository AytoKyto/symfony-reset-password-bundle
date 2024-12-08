<?php

namespace Ayto\ResetPasswordBundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Ayto\ResetPasswordBundle\Processor\TestProcessor;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'test',
    operations: [
        new Post(
            uriTemplate: '/test',
            processor: TestProcessor::class
        ),
    ],
    formats: ['json']
)]
class Test
{
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: "L'email n'est pas valide")]
    public string $email;
}
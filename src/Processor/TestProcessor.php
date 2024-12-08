<?php

namespace Ayto\ResetPasswordBundle\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Ayto\ResetPasswordBundle\ApiResource\Test;

class TestProcessor implements ProcessorInterface
{
    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): Test
    {
        // Logique de traitement ici
        // Par exemple, vérifier l'email, envoyer un email, etc.

        // Retournez l'objet traité
        return $data;
    }
}
<?php

namespace Ayto\ResetPasswordBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class ResetPasswordRequestController extends AbstractController
{
    public function __invoke(): false|string
    {
        return 'Hellow';
    }
}
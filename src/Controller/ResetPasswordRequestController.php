<?php
declare(strict_types=1);

namespace Ayto\ResetPasswordBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ResetPasswordRequestController extends AbstractController
{
    public function __invoke(): false|string
    {
        return 'Hellow';

    }
}
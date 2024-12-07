<?php

namespace Ayto\ResetPasswordBundle\Controller;

use Ayto\ResetPasswordBundle\Service\ResetPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource]
class ResetPasswordController extends AbstractController
{
    public function __construct(
        private ResetPasswordService $resetPasswordService,
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/reset-password', name: 'app_reset_password_request')]
    public function requestPage(): Response
    {
        return $this->render('@ResetPassword/reset_password/request.html.twig');
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password_reset')]
    public function resetPage(string $token): Response
    {
        return $this->render('@ResetPassword/reset_password/reset.html.twig', [
            'token' => $token
        ]);
    }

    #[Route('/api/reset-password/request', name: 'api_reset_password_request', methods: ['POST'])]
    public function requestReset(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $email = $content['email'] ?? null;

        if (!$email) {
            return new JsonResponse(['error' => 'Email is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $token = $this->resetPasswordService->generateResetToken($email);
            
            if (!$token) {
                return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            
            $this->resetPasswordService->sendResetEmail($email, $token);

            return new JsonResponse(['message' => 'Reset password email sent']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/reset-password/reset', name: 'api_reset_password_reset', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        $token = $content['token'] ?? null;
        $newPassword = $content['password'] ?? null;

        if (!$token || !$newPassword) {
            return new JsonResponse(['error' => 'Token and password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->resetPasswordService->validateTokenAndFetchUser($token);
        
        if (!$user) {
            return new JsonResponse(['error' => 'Invalid or expired token'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            
            $this->getDoctrine()->getManager()->flush();

            return new JsonResponse(['message' => 'Password has been reset successfully']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'An error occurred while resetting the password'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

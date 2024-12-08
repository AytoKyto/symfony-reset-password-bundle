<?php

namespace Ayto\ResetPasswordBundle\Controller;

use ApiPlatform\Metadata\ApiResource;
use Ayto\ResetPasswordBundle\Service\ResetPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[ApiResource]
class ResetPasswordController extends AbstractController
{
    public function __construct(
        private readonly ResetPasswordService $resetPasswordService,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/reset-password', name: 'app_reset_password_index')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_reset_password_request');
    }

    #[Route('/reset-password/request', name: 'app_reset_password_request', methods: ['GET', 'POST'])]
    public function request(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $this->resetPasswordService->sendResetEmail($email);

            $this->addFlash('success', 'Si un compte existe avec cet email, un lien de réinitialisation vous a été envoyé.');
            return $this->redirectToRoute('app_reset_password_request');
        }

        return $this->render('@ResetPassword/reset_password/request.html.twig');
    }

    #[Route('/reset-password/reset/{token}', name: 'app_reset_password_reset', methods: ['GET', 'POST'])]
    public function reset(Request $request, string $token): Response
    {
        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            
            try {
                $this->resetPasswordService->resetPassword($token, $password);
                $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
                return $this->redirectToRoute('app_login');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        }

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

<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;

class AuthenticationController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, JWTTokenManagerInterface $JWTManager, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger, SessionInterface $session): JsonResponse
    {
        $logger->debug('Logger is working!');

        $data = json_decode($request->getContent(), true);
        $logger->info('Login request received.', ['data' => $data]);
        if (!isset($data['login'], $data['password'])) {

            $logger->error('Missing login or password.');
            return new JsonResponse(['error' => 'Missing login or password'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $data['login']]);

        if (!$user) {
            $logger->warning('Invalid credentials: user not found.', ['login' => $data['login']]);
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Validation du mot de passe
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            $logger->warning('Invalid credentials: password mismatch.', ['login' => $data['login']]);
            return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $JWTManager->create($user);
        $logger->info('User authenticated successfully.', ['login' => $data['login'], 'token' => $token]);

        $session->set('user', [
            'login' => $user->getLogin(),
            'nom' => $user->getNom(),
            'role' => $user->getRole(),
            'id' => $user->getId(),
        ]);
        $logger->info('User session set successfully.', ['session_user' => $session->get('user')]);
        return new JsonResponse([
            'message' => 'Authentication successful',
            'token' => $token,
            'user' => [
                'login' => $user->getLogin(),
                'nom' => $user->getNom(),
                'role' => $user->getRole(),
                'id' => $user->getId(),
            ],
        ], JsonResponse::HTTP_OK);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(SessionInterface $session): JsonResponse
    {
        $session->invalidate(); // Détruit toutes les données de la session
        return new JsonResponse(['message' => 'Logout successful']);
    }



    #[Route('/api/check-session', name: 'api_check_session', methods: ['GET'])]
    public function checkSession(SessionInterface $session): JsonResponse
    {
        $user = $session->get('user');
        if (!$user) {
            return new JsonResponse(['authenticated' => false], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['authenticated' => true, 'user' => $user]);
    }

}

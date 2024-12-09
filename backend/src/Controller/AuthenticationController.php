<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Psr\Log\LoggerInterface;

class AuthenticationController extends AbstractController
{
        #[Route('/api/login', name: 'api_login', methods: ['POST'])]
        public function login(Request $request, JWTTokenManagerInterface $JWTManager, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher, LoggerInterface $logger): JsonResponse
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
        
            return new JsonResponse([
                'message' => 'Authentication successful',
                'token' => $token,
                'user' => [
                            'login' => $user->getLogin(),
                            'nom' => $user->getNom(),
                            'role' => $user->getRole(),
    ],
            ], JsonResponse::HTTP_OK);
        }
        
}

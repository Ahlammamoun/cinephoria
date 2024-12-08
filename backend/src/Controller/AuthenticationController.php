<?php
namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Password\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthenticationController extends AbstractController
{
        #[Route('/api/login', name: 'api_login', methods: ['POST'])]
        public function login(Request $request, JWTTokenManagerInterface $JWTManager, EntityManagerInterface $em, PasswordHasherInterface $passwordHasher): JsonResponse
        {
            $data = json_decode($request->getContent(), true);
    
            if (!isset($data['login'], $data['password'])) {
                return new JsonResponse(['error' => 'Missing login or password'], JsonResponse::HTTP_BAD_REQUEST);
            }
    
            $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $data['login']]);
    
            if (!$user) {
                return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
            }
    
            // Validation du mot de passe
            if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
                return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
            }
    
            $token = $JWTManager->create($user);
    
            return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
        }
        
}

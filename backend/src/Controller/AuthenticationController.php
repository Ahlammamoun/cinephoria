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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Uid\Uuid;

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
        // if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
        //     $logger->warning('Invalid credentials: password mismatch.', ['login' => $data['login']]);
        //     return new JsonResponse(['error' => 'Invalid credentials'], JsonResponse::HTTP_UNAUTHORIZED);
        // }

        $token = $JWTManager->create($user);
        $logger->info('User authenticated successfully.', ['login' => $data['login'], 'token' => $token]);

        $session->set('user', [
            'login' => $user->getLogin(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
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
                'prenom' => $user->getPrenom(),
                'role' => $user->getRole(),
                'id' => $user->getId(),
            ],
        ], JsonResponse::HTTP_OK);
    }



    #[Route('/api/forgot-password', name: 'api_forgot_password', methods: ['POST'])]
    public function forgotPassword(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
    
        // Vérifie si le login est fourni
        if (!isset($data['login'])) {
            return new JsonResponse(['error' => 'Veuillez fournir votre identifiant (email).'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Recherche l'utilisateur par login
        $user = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $data['login']]);
    
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé.'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        // Vérifie si l'email est valide
        if (!filter_var($user->getLogin(), FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['error' => 'Adresse email invalide.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Générer un mot de passe temporaire
        $temporaryPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $hashedPassword = $passwordHasher->hashPassword($user, $temporaryPassword);
    
        // Mettre à jour l'utilisateur avec le mot de passe temporaire
        $user->setPassword($hashedPassword);
        $user->setRequiresPasswordChange(true); // Nécessite un changement de mot de passe
        $em->persist($user);
        $em->flush();
    
        // Envoyer un email avec le mot de passe temporaire
        $email = (new Email())
            ->from('no-reply@cinema.com')
            ->to($user->getLogin()) // Utiliser login comme email
            ->subject('Réinitialisation de votre mot de passe')
            ->text("Votre nouveau mot de passe temporaire est : $temporaryPassword\nVeuillez le modifier dès votre connexion.");
    
        $mailer->send($email);
    
        return new JsonResponse(['message' => 'Mot de passe temporaire envoyé par e-mail.'], JsonResponse::HTTP_OK);
    }
    

    #[Route('/api/change-password', name: 'api_change_password', methods: ['POST'])]
    public function changePassword(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        SessionInterface $session
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $loggedInUser = $session->get('user');
    
        if (!$loggedInUser) {
            return new JsonResponse(['error' => 'User not authenticated'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    
        $user = $em->getRepository(Utilisateur::class)->find($loggedInUser['id']);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        // Valider les mots de passe
        if (!isset($data['oldPassword'], $data['newPassword'])) {
            return new JsonResponse(['error' => 'Missing parameters'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        if (!$passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
            return new JsonResponse(['error' => 'Invalid current password'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    
        // Modifier le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
        $user->setPassword($hashedPassword);
        $user->setRequiresPasswordChange(false); // Marquer comme modifié
        $em->persist($user);
        $em->flush();
    
        return new JsonResponse(['message' => 'Password changed successfully'], JsonResponse::HTTP_OK);
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

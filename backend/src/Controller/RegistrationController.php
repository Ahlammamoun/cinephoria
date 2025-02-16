<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            // Validate the data
            if (!isset($data['login'], $data['password'], $data['prenom'], $data['nom'], $data['role'])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }

            // Validate the role
            $validRoles = ['user', 'admin', 'employe']; // Added ROLE_EMPLOYE
            if (!in_array($data['role'], $validRoles)) {
                return new JsonResponse(['error' => 'Invalid role'], Response::HTTP_BAD_REQUEST);
            }

            // Vérifie si l'utilisateur existe déjà dans la base
            $existingUser = $em->getRepository(Utilisateur::class)->findOneBy(['login' => $data['login']]);
            if ($existingUser) {
                return new JsonResponse(['error' => 'Login already exists'], Response::HTTP_CONFLICT); // HTTP 409
            }

            // Create the user
            $user = new Utilisateur();
            $user->setLogin($data['login']);
            $user->setPrenom($data['prenom']);
            $user->setNom($data['nom']);
            $user->setRole($data['role']);
            $user->setPassword($passwordHasher->hashPassword($user, $data['password']));

            // Persist the user
            $em->persist($user);
            $em->flush();


            return new JsonResponse(['message' => 'User created successfully'], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

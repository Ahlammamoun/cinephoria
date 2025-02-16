<?php

namespace App\Controller;

use App\Entity\ContactRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/api/contact', name: 'contact_create', methods: ['POST'])]
    public function createContact(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $title = $data['title'] ?? null;
        $description = $data['description'] ?? null;

        if (!$title || !$description) {
            return new JsonResponse(['error' => 'Title and description are required'], 400);
        }

        $contactRequest = new ContactRequest();
        $contactRequest->setUsername($username);
        $contactRequest->setTitle($title);
        $contactRequest->setDescription($description);

        $em->persist($contactRequest);
        $em->flush();

        return new JsonResponse(['message' => 'Contact request submitted successfully'], 201);
    }
}

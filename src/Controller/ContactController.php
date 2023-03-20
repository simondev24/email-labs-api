<?php

namespace App\Controller;

use App\Service\ContactManager;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/contacts', name: 'app_contacts')]
class ContactController extends AbstractController
{
    public function __construct(private ContactManager $contactManager) {}

    #[Route('/', methods: 'GET')]
    public function getContacts(): Response
    {
        return new JsonResponse($this->contactManager->listContacts());
    }

    #[Route('/{id}', methods: 'GET')]
    public function getContact(int $id): Response
    {
        return new JsonResponse($this->contactManager->getContactById($id));
    }

    /**
     * @throws Exception
     */
    #[Route('', methods: 'POST')]
    public function postContact(Request $request): Response
    {
        return $this->contactManager->createContact(json_decode($request->getContent(), true));
    }

    #[Route('/{id}', methods: 'DELETE')]
    public function deleteContact(string $id): Response
    {
        return $this->contactManager->deleteContact(json_decode($id, true));
    }
}

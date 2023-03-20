<?php

namespace App\Service;

use App\Validator\ContactValidator;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class ContactManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContactValidator $validator,
        private readonly DbManager $dbManager
    ) {}

    /**
     * @throws Exception
     */
    public function listContacts(): array
    {
        return $this->entityManager->getConnection()->prepare("SELECT * FROM contacts")->executeQuery()->fetchAllAssociative();
    }

    /**
     * @throws Exception
     */
    public function getContactById(int $contactId): Response
    {
        $sql = "SELECT * FROM contacts WHERE contacts.id = :id";
        $dbalResult = $this->dbManager->executeQuery($sql, ['id' => $contactId]);
        $contact = $dbalResult->fetchAllAssociative();
        return $contact ? new Response(content: json_encode($contact[0]), status: 200) : new Response('Contact was not found', status: 404);
    }

    /**
     * @throws Exception
     */
    public function deleteContact(int $contactId): Response
    {
        $sql = 'DELETE FROM contacts WHERE id = :id';
        return $this->dbManager->executeQuery($sql, ['id' => $contactId]) ? new Response(status: 204) : new Response('Contact was not found', 404);
    }

    /**
     * @throws Exception
     */
    public function createContact(array $parameters): Response {
        $parameters = $parameters[0];
        $errors = $this->validator->validate($parameters['email'], $parameters['firstName'], $parameters['lastName']);
        if ($this->emailAlreadyExists($parameters['email'])) {
            $errors[] = 'Email already exists!';
        }

        if (count($errors)) {
            return new Response(json_encode($errors), 400);
        }
        $parameters['id'] = $this->dbManager->getMaximumIdForContacts() + 1;
        $sql = 'INSERT INTO contacts VALUES (:id, :email, :firstName, :lastName, NOW(), NOW())';
        $this->dbManager->executeQuery($sql, $parameters);
        return new Response(status: 204);
    }

    /**
     * @throws Exception
     */
    public function getEmailsByContactIds(array $contactIds): array {
        $messageId = 20032023;

        $queryResult = $this->entityManager->getConnection()->executeQuery('SELECT email FROM contacts WHERE id IN (?)',
            [$contactIds['contactIds']],
            [ArrayParameterType::INTEGER]
        );
        $resultEmails = array_merge(...$queryResult->fetchAllAssociative());
        return array_fill_keys($resultEmails, ['message_id' => $messageId]);
    }

    /**
     * @throws Exception
     */
    public function emailAlreadyExists(string $email): Result
    {
        $sql = 'SELECT 1 FROM contacts WHERE email = :email LIMIT 1';
        $queryParameters['email'] = $email;
        return $this->dbManager->executeQuery($sql, $queryParameters);
    }
}
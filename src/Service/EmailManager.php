<?php

namespace App\Service;

use Doctrine\DBAL\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

class EmailManager
{
    private const API_KEY = '';

    private const API_SECRET = '';

    private const URL = 'https://api.emaillabs.net.pl/api/';

    public function __construct(private readonly ContactManager $contactManager) {}

    private function generateAuthToken(): string {
        return 'Basic ' . base64_encode(self::API_KEY . ':' . self::API_SECRET);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function sendEmailForSelectedContacts(array $payload): Response {
        $client = new Client();
        $emails = $this->contactManager->getEmailsByContactIds(current($payload));
        try {
            $response = $client->request(
                'POST',
                self::URL . 'new_sendmail',
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                        'Authorization' => $this->generateAuthToken()
                    ],
                    'form_params' => $this->getRequestParams($emails)
                ]
            );
        } catch (\Exception $exception) {
            return new Response($exception->getMessage(), 500);
        }

        if ($response->getStatusCode() !== 200) {
            return new Response('Failed to send email', 500);
        }
        return new Response ($response->getBody(), 200);
    }

    private function getRequestParams(array $emails): array {
        return [
            'to' => $emails,
            'smtp_account' => '1.szymon.smtp',
            'subject' => 'Test subject',
            'html' => '<p>This e-mail was sent using EmailLabs</p>',
            'text' => 'This e-mail was sent using EmailLabs',
            'from' => 'szymon9712@gmail.com'
        ];
    }

}
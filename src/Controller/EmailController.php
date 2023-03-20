<?php

namespace App\Controller;


use App\Service\EmailManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailController extends AbstractController
{
    #[Route('/api/emails/send',  methods: 'POST')]
    public function sendEmails(Request $request, EmailManager $emailManager): Response
    {
        return $emailManager->sendEmailForSelectedContacts(json_decode($request->getContent(), true));
    }

}

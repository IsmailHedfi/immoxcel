<?php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use App\Form\AddMailType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

class MailController extends AbstractController
{
    #[Route('/mail', name: 'app_mail')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(AddMailType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $formData = $form->getData();
        $email = (new Email())
            ->from($formData['senderEmail'])
            ->to($formData['recipientEmail'])
            ->subject($formData['subject'])
            ->html($formData['message']);

        $transport = new GmailSmtpTransport('chiboub.ghalia@gmail.com', 'twsv pulq brpo nfes');
        $mailer = new Mailer($transport);
        
        $mailer->send($email);
        return $this->redirectToRoute('app_employees'); // Redirect to a success page after sending the email
    
}
        return $this->render('mail/index.html.twig', ['formAdd'=>$form->createView()]);
    }
}

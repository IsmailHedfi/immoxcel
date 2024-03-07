<?php
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendMailService
    {
        private $mailer;
        public function __construct(MailerInterface $mailer)
            {
                $this->mailer=$mailer;
            }
        public function SendMail(string $from , string $to , string $subject , string $template, array $context):void
            {
                #create Mail
                    $email = (new TemplatedEmail())
                    ->from($from)
                    ->to($to)
                    ->subject($subject)
                    ->htmlTemplate("admin/$template.html.twig")
                    ->context($context);
                #send mail
                    $this->mailer->send($email);
            }
    }
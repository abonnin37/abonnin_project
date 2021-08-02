<?php


namespace App\Controller;


use App\Entity\ContactMail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

class SendContactEmail extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(ContactMail $data)
    {
        $email = (new TemplatedEmail())
            ->from($data->getEmail())
            ->to("bonnin.a.k@gmail.com")
            ->subject($data->getSubject())
            ->htmlTemplate('emails/contactMail.html.twig')
            ->context([
                'firstName'=>$data->getFirstName(),
                'lastName'=>$data->getLastName(),
                'fromEmail'=>$data->getEmail(),
                'company'=>$data->getCompany(),
                'subject'=>$data->getSubject(),
                'message'=>$data->getMessage(),
            ]);
        $this->mailer->send($email);

        return new Response("OK");
    }
}
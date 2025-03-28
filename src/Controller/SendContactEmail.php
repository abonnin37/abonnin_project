<?php

namespace App\Controller;

use App\Entity\ContactMail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SendContactEmail extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    public function __invoke(ContactMail $data, ValidatorInterface $validator): Response
    {
        // We check if there is any error in the entity before sending the email
        $errors = $validator->validate($data);

        if (count($errors) > 0) {
            $errorsString = implode("\n", array_map(
                fn($error) => $error->getMessage(),
                iterator_to_array($errors)
            ));

            return new Response(
                json_encode(["message" => $errorsString]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        $email = (new TemplatedEmail())
            ->from($_ENV['SERVER_EMAIL'])
            ->to("bonnin.a.k@gmail.com")
            ->subject("[alexandrebonnin.fr] Nouveau message du formulaire de contact")
            ->htmlTemplate('emails/contactMail.html.twig')
            ->context([
                'firstName' => $data->getFirstName(),
                'lastName' => $data->getLastName(),
                'fromEmail' => $data->getEmail(),
                'company' => $data->getCompany(),
                'subject' => $data->getSubject(),
                'message' => $data->getMessage(),
            ]);

        try {
            $this->mailer->send($email);
            return new Response("OK");
        } catch (\Exception $e) {
            return new Response(
                json_encode(["message" => "Une erreur est survenue lors de l'envoi de l'email"]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json'],
            );
        }
    }
}
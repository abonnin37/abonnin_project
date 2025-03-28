<?php

namespace App\Controller;

use App\Entity\ProspectMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;

class SendProspectEmail extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    public function __invoke(ProspectMail $data, EntityManagerInterface $entityManager): Response
    {
        try {
            // We persist the email prospect in the database
            $prospectMail = new ProspectMail();
            $prospectMail->setEmail($data->getEmail());
            $prospectMail->setCreatedAt(new \DateTime());

            $entityManager->persist($prospectMail);
            $entityManager->flush();

            // We send the mail with the document to the prospect
            $filePath = __DIR__."/../../public/images/curriculum/curriculum.pdf";

            if (!file_exists($filePath)) {
                throw new \RuntimeException('Le fichier curriculum.pdf n\'existe pas');
            }

            $email = (new TemplatedEmail())
                ->from($_ENV['SERVER_EMAIL'])
                ->to($data->getEmail())
                ->subject("[alexandrebonnin.fr] Document reçu")
                ->htmlTemplate('emails/prospectMail.html.twig')
                ->attachFromPath($filePath);

            $this->mailer->send($email);

            return new Response(
                json_encode(["message" => "Email envoyé avec succès"]),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } catch (\Exception $e) {
            return new Response(
                json_encode(["message" => "Une erreur est survenue lors de l'envoi de l'email"]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }
    }
}
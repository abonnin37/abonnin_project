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
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(ProspectMail $data, EntityManagerInterface $entityManager)
    {
        // We persist the email prospect in the database
        $prospectMail = new ProspectMail();
        $prospectMail->setEmail($data->getEmail());
        $prospectMail->setCreatedAt(new \DateTime());

        $entityManager->persist($prospectMail);
        $entityManager->flush();

        // We send the mail with the document to the prospect
        $filePath = __DIR__."/../../public/images/curriculum/curriculum.pdf";

        $email = (new TemplatedEmail())
            ->from("contact@alexandrebonnin.fr")
            ->to($data->getEmail())
            ->subject("Document reçu de alexandrebonnin.fr")
            ->htmlTemplate('emails/prospectMail.html.twig')
            ->attachFromPath($filePath);
        $this->mailer->send($email);

        return new Response("OK");
    }

}
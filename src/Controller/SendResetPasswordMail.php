<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SendResetPasswordMail extends AbstractController
{
    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    public function __invoke(
        User $data,
        TokenGeneratorInterface $tokenGenerator,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Get email sent
        $emailSent = $data->getEmail();
        $user = $userRepository->findOneBy(['email' => $emailSent]);

        // Test if the user exist
        if ($user) {
            try {
                // We generate a token
                $token = $tokenGenerator->generateToken();

                // We save the token in the entity
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // We generate the reset url
                $url = "https://alexandrebonnin.fr/reset-password?id=".$user->getId()."&token=".$token;

                $email = (new TemplatedEmail())
                    ->from($_ENV['SERVER_EMAIL'])
                    ->to($emailSent)
                    ->subject("[alexandrebonnin.fr] Réinitialisation de votre mot de passe")
                    ->htmlTemplate('emails/resetPasswordMail.html.twig')
                    ->context([
                        'link' => $url,
                    ]);

                $this->mailer->send($email);
            } catch (\Exception $e) {
                return new Response(
                    json_encode(["message" => "Une erreur est survenue lors de l'envoi de l'email"]),
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    ['content-type' => 'application/json']
                );
            }
        }

        // We always return OK to avoid telling the user who has an account on our website and who doesn't
        return new Response(
            null,
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
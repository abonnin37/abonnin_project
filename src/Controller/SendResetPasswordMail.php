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
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(User $data, TokenGeneratorInterface $tokenGenerator, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // Get email sent
        $emailSent = $data->getEmail();
        $user = $userRepository->findOneBy(['email' => $emailSent]);

        // Test if the user exist
        if ($user){
            // We generate a token
            $token = $tokenGenerator->generateToken();

            try {
                // We save the token in the entity
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                return new Response(
                    json_encode(["message" => "Une erreur est survenue"]),
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                );
            }

            // We generate the reset url
            $url = "http://localhost:3000/reset-password?id=".$user->getId()."&token=".$token;

            $email = (new TemplatedEmail())
                ->from("bonnin.a.k@gmail.com")
                ->to($emailSent)
                ->subject("Réinitialisation de votre mot de passe")
                ->htmlTemplate('emails/resetPasswordMail.html.twig')
                ->context([
                    'link'=>$url,
                ]);
            $this->mailer->send($email);
        }

        // We always return OK to avoid telling the user who has an account on our website and who doesn't
        return new Response(
            null,
            Response::HTTP_OK,
        );
    }

}
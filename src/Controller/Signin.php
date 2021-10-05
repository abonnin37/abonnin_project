<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class Signin extends AbstractController
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
    )
    {
    }

    public function __invoke(User $data, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator, Request $request)
    {
        $requestContent = json_decode($request->getContent(), true);

        // We test if the two new password are valid
        if ($data->getPassword() !== $requestContent['confirmPassword']) {
            return new Response(
                json_encode(["message" => "Les deux nouveaux mots de passe ne sont pas identiques"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        $errors = $validator->validate($data);

        if (count($errors) > 0) {
            $errorsString = "";

            for($i = 0; $i < count($errors); $i++) {
                $errorsString .= $errors->get($i)->getMessage()."\n";
            }

            return new Response(
                json_encode(["message" => $errorsString]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        // We crypte the password
        $cryptedPassword = $encoder->encodePassword($data, $data->getPassword());
        $data->setPassword($cryptedPassword);
        $data->setVerified(false);

        // When the $data is flushed, symfony actualise it with the new id
        $entityManager->persist($data);
        $entityManager->flush();

        // We set-up the email verification system with the route of the verification function as parameter
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'signin_confirmation_email',
            $data->getId(),
            $data->getEmail(),
            ['id' => $data->getId()] // add the user's id as an extra query param to allow anonymous validation
        );

        $email = (new TemplatedEmail())
            ->from("contact@alexandrebonnin.fr")
            ->to($data->getEmail())
            ->subject("[alexandrebonnin.fr] Lien de validation d'inscription")
            ->htmlTemplate('emails/signinMail.html.twig')
            ->context(['signedUrl' => $signatureComponents->getSignedUrl()]);

        $this->mailer->send($email);

        // we send a response to the api
        return new Response(
            json_encode(["message" => "Un lien a été envoyé à votre adresse email pour valider votre inscription"]),
            Response::HTTP_CREATED,
            ['content-type' => 'application/json'],
        );
    }

    #[Route("/verifyEmail", name: "signin_confirmation_email")]
    public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $id = $request->get('id'); // retrieve the user id from the url

        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirect('http://localhost:3000/login?message="L\'identifiant de l\'utilisateur n\'est pas valide"&status=400');
        }

        $user = $userRepository->find($id);

        // Ensure the user exists in persistence
        if (null === $user) {
            return $this->redirect('http://localhost:3000/login?message="L\'utilisateur n\'est pas inscrit"&status=400');
        }

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            return $this->redirect('http://localhost:3000/login?message='. $e->getReason() .'&status=400');
        }

        // Mark your user as verified.
        $user->setVerified(true);
        $entityManager->persist($user);
        $entityManager->flush();

        // we redirect to the front end
        return $this->redirect('http://localhost:3000/login?message=Votre compte à bien été créé&status=201');
    }
}
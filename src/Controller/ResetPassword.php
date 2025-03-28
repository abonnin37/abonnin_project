<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPassword extends AbstractController
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function __invoke(
        User $data,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): Response {
        $requestContent = json_decode($request->getContent(), true);

        // We test if the token is valid
        if ($data->getResetToken() === null || $data->getResetToken() !== $requestContent["token"]) {
            return new Response(
                json_encode(["message" => "Le token n'est pas valide"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        // We test if the two new password are valid
        if ($requestContent['newPassword'] !== $requestContent['confirmNewPassword']) {
            return new Response(
                json_encode(["message" => "Les deux nouveaux mots de passe ne sont pas identiques"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        //We set the User password to "newPassword" to make the rest of the modifications
        $data->setPassword($requestContent['newPassword']);

        // We check if there is any error in the entity before setting the new password
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

        try {
            $data->setResetToken(null);
            $hashedPassword = $passwordHasher->hashPassword($data, $requestContent['newPassword']);
            $data->setPassword($hashedPassword);

            return new Response(
                json_encode(["message" => "Mot de passe réinitialisé avec succès"]),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } catch (\Exception $e) {
            return new Response(
                json_encode(["message" => "Une erreur est survenue lors de la réinitialisation du mot de passe"]),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }
    }
}
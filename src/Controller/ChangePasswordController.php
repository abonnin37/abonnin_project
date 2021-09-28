<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangePasswordController extends AbstractController
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function __invoke(User $data, Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
        $requestContent = json_decode($request->getContent(), true);

        $oldPassword = $requestContent['oldPassword'];
        if (!$encoder->isPasswordValid($data, $oldPassword)) {
            return new Response(
                json_encode(["message" => "Votre mot de passe actuel n'est pas valide"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        //We set the User password to "newPassword" to make the rest of the modifications
        $data->setPassword($requestContent['newPassword']);

        // We test if the two new password are valid
        if ($data->getPassword() !== $requestContent['confirmNewPassword']) {
            return new Response(
                json_encode(["message" => "Les deux nouveaux mots de passe ne sont pas identiques"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        // We check if there is any error in the entity before setting the new password
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

        $data->setPassword($encoder->encodePassword($data, $data->getPassword()));

        return $data;
    }
}
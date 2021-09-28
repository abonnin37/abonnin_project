<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPassword extends AbstractController
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function __invoke(User $data, Request $request, UserPasswordEncoderInterface $encoder, ValidatorInterface $validator)
    {
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



        $data->setResetToken(null);
        $data->setPassword($encoder->encodePassword($data, $requestContent['newPassword']));

        return $data;
    }

}
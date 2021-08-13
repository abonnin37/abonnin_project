<?php


namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ChangePasswordController extends AbstractController
{
    public function __construct(
        private Security $security
    )
    {
    }

    public function __invoke(User $data, Request $request, UserPasswordEncoderInterface $encoder)
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

        // We test if the two new password are valid
        if ($requestContent['newPassword'] !== $requestContent['confirmNewPassword']) {
            return new Response(
                json_encode(["message" => "Les deux nouveaux mots de passe ne sont pas identiques"]),
                Response::HTTP_BAD_REQUEST,
                ['content-type' => 'application/json'],
            );
        }

        $data->setPassword($encoder->encodePassword($data, $requestContent['newPassword']));

        return $data;
    }
}
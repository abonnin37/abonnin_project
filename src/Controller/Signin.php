<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class Signin extends AbstractController
{
    public function __invoke(User $data, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder)
    {
        $cryptedPassword = $encoder->encodePassword($data, $data->getPassword());
        $data->setPassword($cryptedPassword);

        /*
         * If we send a response back the UniqueEntity error will not be send. We just need to send back the modified data.
         *
        $entityManager->persist($data);
        $entityManager->flush();

        return new Response(
            json_encode(["message" => "Votre compte à bien été créé"]),
            Response::HTTP_CREATED,
            ['content-type' => 'application/json'],
        );*/

        return $data;
    }
}
<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class MeController extends AbstractController
{
    public function __construct(
        private readonly Security $security
    ) {
    }

    public function __invoke(UserRepository $userRepository): Response
    {
        try {
            $securityUser = $this->security->getToken()->getUser();
            $user = $userRepository->loadUserByUsername($securityUser->getUsername());

            if (!$user) {
                return new Response(
                    json_encode(['message' => 'Utilisateur non trouvé']),
                    Response::HTTP_NOT_FOUND,
                    ['content-type' => 'application/json']
                );
            }

            return new Response(
                json_encode([
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'email' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                ]),
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        } catch (\Exception $e) {
            return new Response(
                json_encode(['message' => 'Une erreur est survenue lors de la récupération des informations utilisateur']),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['content-type' => 'application/json']
            );
        }
    }
}
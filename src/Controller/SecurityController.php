<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return new Response(
                json_encode(['message' => 'Utilisateur non authentifié']),
                Response::HTTP_UNAUTHORIZED,
                ['content-type' => 'application/json']
            );
        }

        return new Response(
            json_encode([
                'username' => $user->getUsername(),
                'roles' => $user->getRoles(),
            ]),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    #[Route(path: '/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): Response
    {
        return new Response(
            json_encode(['message' => 'Déconnexion réussie']),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
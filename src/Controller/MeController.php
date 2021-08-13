<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Security;

class MeController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(UserRepository $userRepository)
    {
        /** @var User $user */
        $securityUser = $this->security->getToken()->getUser();
        // We can't get the complete user from the security method
        // See this https://symfony.com/doc/current/security/user_provider.html#using-a-custom-query-to-load-the-user
        $user = $userRepository->loadUserByUsername($securityUser->getUsername());

        return $user;
    }

}
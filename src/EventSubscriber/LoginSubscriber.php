<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class LoginSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function onLoginSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var User $user */
        $user = $event->getUser();

        // We verify if the user is activated
        // see for more detail : https://symfony.com/doc/current/security/guard_authentication.html#customizing-error-messages
        if (!$user->getVerified()) {
            throw new CustomUserMessageAuthenticationException("Votre compte n'est pas activé");
        }

        // We update the last login date
        $user->setLastLogin(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            "lexik_jwt_authentication.on_authentication_success" => 'onLoginSuccess'
        ];
    }
}

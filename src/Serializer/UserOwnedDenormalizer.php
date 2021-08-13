<?php


namespace App\Serializer;


use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

// Le but de cette classe est de capter les opérations de dénormalisation et d'injecter l'utilisateur actuel automatiquement à la requête
class UserOwnedDenormalizer implements  ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    // Nous permets d'être en adhéquation avec l'interface DenormalizerAwareInterface
    use DenormalizerAwareTrait;

    private const ALREADY_CALLED = 'USER_CALLED_DENORMALIZER_ALREADY_CALLED';

    public function __construct(private Security $security)
    {
    }

    // Est ce que la requette suporte la dénormalisation
    public function supportsDenormalization($data, string $type, string $format = null, array $context = [])
    {
        $reflectionClass = new \ReflectionClass($type);

        $alreadyCalled = $context[$this->getAlreadyCalledKey($type)] ?? false;

        return $reflectionClass->implementsInterface(UserOwnedInterface::class) && $alreadyCalled === false;
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        $context[$this->getAlreadyCalledKey($type)] = true;
        /** @var UserOwnedInterface $obj */
        $obj = $this->denormalizer->denormalize($data, $type, $context);


        $obj->setUser($this->security->getUser());

        return $obj;
    }

    private function getAlreadyCalledKey(string $type) {
        return self::ALREADY_CALLED . $type;
    }
}
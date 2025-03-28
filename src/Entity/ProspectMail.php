<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\SendProspectEmail;
use App\Repository\ProspectMailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProspectMailRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/prospect_mails/send',
            controller: SendProspectEmail::class,
            denormalizationContext: ['groups' => ['write:ProspectMail:collection']]
        ),
    ]
)]
class ProspectMail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:ProspectMail:item'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email(
        message: "L'email {{ value }} n'est pas un email valide.",
    )]
    #[Groups(['write:ProspectMail:collection', 'read:ProspectMail:item'])]
    private ?string $email = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:ProspectMail:item'])]
    private ?\DateTimeInterface $created_at = null;

    public function __construct()
    {
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }
}

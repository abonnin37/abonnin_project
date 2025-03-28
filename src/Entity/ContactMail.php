<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Controller\SendContactEmail;
use App\Repository\ContactMailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactMailRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/contact_mails/send',
            controller: SendContactEmail::class,
        ),
    ]
)]
class ContactMail
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:ContactMail:item'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le prénom doit avoir au moins  {{ limit }} caractères.',
        maxMessage: 'Le prénom doit avoir au plus  {{ limit }} caractères.',
    )]
    #[Groups(['read:ContactMail:item'])]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit avoir au moins  {{ limit }} caractères.',
        maxMessage: 'Le nom doit avoir au plus  {{ limit }} caractères.',
    )]
    #[Groups(['read:ContactMail:item'])]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "L'email doit avoir au moins  {{ limit }} caractères.",
        maxMessage: "L'email doit avoir au plus  {{ limit }} caractères.",
    )]
    #[Assert\Email(
        message: "L'email {{ value }} n'est pas un email valide.",
    )]
    #[Groups(['read:ContactMail:item'])]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le sujet doit avoir au moins  {{ limit }} caractères.',
        maxMessage: 'Le sujet doit avoir au plus  {{ limit }} caractères.',
    )]
    #[Groups(['read:ContactMail:item'])]
    private ?string $subject = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 20,
        minMessage: 'Le message doit avoir au moins  {{ limit }} caractères.',
    )]
    #[Groups(['read:ContactMail:item'])]
    private ?string $message = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[Groups(['read:ContactMail:item'])]
    private ?string $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
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

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;
        return $this;
    }
}

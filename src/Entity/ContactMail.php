<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\SendContactEmail;
use App\Repository\ContactMailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ContactMailRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        "post_contact_mail" => [
            'method' => 'POST',
            'path' => '/contact_mails/send',
            'controller' => SendContactEmail::class,
        ],
    ],
    itemOperations: []
)]
class ContactMail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le prénom doit avoir au moins  {{ limit }} caractères.',
        maxMessage: 'Le prénom doit avoir au plus  {{ limit }} caractères.',
    )]
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit avoir au moins  {{ limit }} caractères.',
        maxMessage: 'Le nom doit avoir au plus  {{ limit }} caractères.',
    )]
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50)
     */
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'Le sujet doit avoir au moins  {{ limit }} caractères.',
        maxMessage: 'Le sujet doit avoir au plus  {{ limit }} caractères.',
    )]
    private $subject;

    /**
     * @ORM\Column(type="text")
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 20,
        minMessage: 'Le message doit avoir au moins  {{ limit }} caractères.',
    )]
    private $message;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $company;

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

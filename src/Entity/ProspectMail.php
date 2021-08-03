<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\SendProspectEmail;
use App\Repository\ProspectMailRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProspectMailRepository::class)
 */
#[ApiResource(
    collectionOperations: [
    "post_prospect_mail" => [
        'method' => 'POST',
        'path' => '/prospect_mails/send',
        'controller' => SendProspectEmail::class,
        'denormalization_context' => ['groups' => ['write:ProspectMail:collection']]
    ],
],
    itemOperations: []
)]
class ProspectMail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['write:ProspectMail:collection'])]
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

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

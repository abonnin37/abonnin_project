<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\ChangePasswordController;
use App\Controller\MeController;
use App\Controller\ResetPassword;
use App\Controller\SendResetPasswordMail;
use App\Controller\Signin;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email", message="L'email utilisé n'est pas disponible")
 */
#[ApiResource(
    collectionOperations: [
        "get" => ["security" => "is_granted('ROLE_ADMIN')"],
        "post" => [
            'controller' => Signin::class,
            "normalization_context" => ["groups" => ["write:User:collection"]]
        ],
        "postApiResetPasswordSendEmail" => [
            'path' => '/resetPassword',
            'method' => 'POST',
            'controller' => SendResetPasswordMail::class,
            'openapi_context' => [
                'summary' => "Envoie d'un email à l'utilisateur pour la réinitialisation du mot de passe",
                'description' => "Prend en paramètre l'adrese email de l'utilisateur puis envoie un email si l'utilisateur à bien un comtpe en base de données.",
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'exemple' => 'exemple@email.com',
                                    ],
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ],
    ],
    itemOperations: [
        "get",
        "patch" => [
            "security" => "is_granted('ROLE_USER')",
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ],
        ],
        "me" => [
            "security" => "is_granted('ROLE_USER')",
            'pagination_enabled' => false,
            'path' => '/me',
            'method' => 'GET',
            'controller' => MeController::class,
            'read' => false,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ],
        ],
        "changePassword" => [
            "security" => "is_granted('ROLE_USER')",
            'pagination_enabled' => false,
            'path' => '/users/{id}/changePassword',
            'method' => 'PATCH',
            'controller' => ChangePasswordController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ],
        ],
        "resetPassword" => [
            'pagination_enabled' => false,
            'path' => '/users/{id}/resetPassword',
            'method' => 'PATCH',
            'controller' => ResetPassword::class,
            'openapi_context' => [
                'summary' => "Met à jour le mot de passe de l'utilisateur",
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'newPassword' => [
                                        'type' => 'string',
                                        'exemple' => 'password',
                                    ],
                                    'confirmNewPassword' => [
                                        'type' => 'string',
                                        'exemple' => 'password',
                                    ],
                                    'token' => [
                                        'type' => 'string',
                                        'exemple' => '8T-HmXQeImSPnbLoLvxOESRUk1SnTLY-4nrLW7_UDLk',
                                    ],
                                ],
                            ]
                        ]
                    ]
                ],
            ],
        ],
    ],
    normalizationContext: ["groups" => ["read:User:item"]],
)]
class User implements UserInterface, JWTUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:User:item'])]
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[Groups(['read:User:item'])]
    private $first_name;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[Groups(['read:User:item'])]
    private $last_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    #[Groups(['read:User:item', 'write:User:collection'])]
    #[Assert\NotBlank(
        message: "L'email est requis."
    )]
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
    #[Groups(['write:User:collection'])]
    #[Assert\NotBlank(
        message: "Le mot de passe est requis."
    )]
    #[Assert\Regex(
        pattern: '/\d/',
        message: "Le mot de passe doit contenir au moins un chiffre.",
    )]
    #[Assert\Regex(
        pattern: '/[a-z]/',
        message: "Le mot de passe doit contenir au moins une minuscule.",
    )]
    #[Assert\Regex(
        pattern: '/[A-Z]/',
        message: "Le mot de passe doit contenir au moins une majuscule.",
    )]
    #[Assert\Regex(
        pattern: '/[@\!"\#\(\)\*\/\:;=\|~\[\]\?]/',
        message: 'Le mot de passe doit contenir au moins un de ces caractère spéciaux : @!"#()*/:;=|~[]?',
    )]
    #[Assert\Length(
        min: 8,
        max: 255,
        minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractère.",
        maxMessage: "Le mot de passe doit contenir au plus {{ limit }} caractère."
    )]
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $registered_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="user")
     */
    #[ApiSubresource()]
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity=Project::class, mappedBy="user")
     */
    // https://api-platform.com/docs/core/subresources/#limiting-depth
    #[ApiSubresource(
        maxDepth: 1,
    )]
    private $projects;

    /**
     * @ORM\OneToMany(targetEntity=Citation::class, mappedBy="user")
     */
    private $citations;

    /**
     * @ORM\Column(type="array")
     */
    #[Groups(['read:User:item'])]
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $verified;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $reset_token;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->projects = new ArrayCollection();
        $this->citations = new ArrayCollection();
        $this->registered_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): self
    {
        $this->last_name = $last_name;

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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRegisteredAt(): ?\DateTimeInterface
    {
        return $this->registered_at;
    }

    public function setRegisteredAt(\DateTimeInterface $registered_at): self
    {
        $this->registered_at = $registered_at;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->last_login;
    }

    public function setLastLogin(?\DateTimeInterface $last_login): self
    {
        $this->last_login = $last_login;

        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|project[]
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setUser($this);
        }

        return $this;
    }

    public function removeProject(project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getUser() === $this) {
                $project->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Citation[]
     */
    public function getCitations(): Collection
    {
        return $this->citations;
    }

    public function addCitation(Citation $citation): self
    {
        if (!$this->citations->contains($citation)) {
            $this->citations[] = $citation;
            $citation->setUser($this);
        }

        return $this;
    }

    public function removeCitation(Citation $citation): self
    {
        if ($this->citations->removeElement($citation)) {
            // set the owning side to null (unless already changed)
            if ($citation->getUser() === $this) {
                $citation->setUser(null);
            }
        }

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        // Garantie que chaque user à le role ROLE_USER
        $roles[] = "ROLE_USER";

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->getEmail();
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public static function createFromPayload($id, array $payload)
    {
        return (new User())->setId($id)->setEmail($payload['email'] ?? '');
    }

    public function getVerified(): ?bool
    {
        return $this->verified;
    }

    public function setVerified(bool $verified): self
    {
        $this->verified = $verified;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->reset_token;
    }

    public function setResetToken(?string $reset_token): self
    {
        $this->reset_token = $reset_token;

        return $this;
    }
}

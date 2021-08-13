<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\ChangePasswordController;
use App\Controller\MeController;
use App\Controller\Signin;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
#[ApiResource(
    collectionOperations: [
        "get",
        "post" => [
            'controller' => Signin::class,
            "normalization_context" => ["groups" => ["write:User:collection"]]
        ],
    ],
    itemOperations: [
        "get",
        "patch" => [
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
            ],
        ],
        "me" => [
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
            'pagination_enabled' => false,
            'path' => '/users/{id}/changePassword',
            'method' => 'PATCH',
            'controller' => ChangePasswordController::class,
            'openapi_context' => [
                'security' => [['bearerAuth' => []]]
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['write:User:collection'])]
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
}

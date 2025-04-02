<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Controller\EmptyController;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
#[ApiResource(
    operations: [
        new ApiPost(
            security: "is_granted('ROLE_ADMIN')",
            controller: EmptyController::class,
            normalizationContext: ["groups" => ["read:Post:item"]],
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                "requestBody" => [
                    "content" => [
                        "multipart/form-data" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "imageFile" => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ],
                                    "user" => [
                                        "type" => "string",
                                    ],
                                    "title" => [
                                        "type" => "string",
                                    ],
                                    "summary" => [
                                        "type" => "string",
                                    ],
                                    "content" => [
                                        "type" => "string",
                                    ],
                                    "published" => [
                                        "type" => "string",
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ),
        new GetCollection(
            normalizationContext: ["groups" => ["read:Post:item"]],
            paginationEnabled: true,
            paginationItemsPerPage: 10,
            paginationMaximumItemsPerPage: 100
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: ['security' => [['bearerAuth' => []]]]
        ),
        new Get(
            normalizationContext: ["groups" => ["read:Post:item"]]
        ),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            controller: EmptyController::class,
            normalizationContext: ["groups" => ["read:Post:item"]],
            openapiContext: [
                'security' => [['bearerAuth' => []]],
                "requestBody" => [
                    "content" => [
                        "multipart/form-data" => [
                            "schema" => [
                                "type" => "object",
                                "properties" => [
                                    "imageFile" => [
                                        "type" => "string",
                                        "format" => "binary"
                                    ],
                                    "user" => [
                                        "type" => "string",
                                    ],
                                    "title" => [
                                        "type" => "string",
                                    ],
                                    "summary" => [
                                        "type" => "string",
                                    ],
                                    "content" => [
                                        "type" => "string",
                                    ],
                                    "published" => [
                                        "type" => "string",
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        )
    ],
    order: ['created_at' => 'DESC']
)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Post:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Post:item'])]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Post:item'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:Post:item'])]
    private ?string $summary = null;

    #[Vich\UploadableField(mapping: 'post_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $imageName = null;

    #[Groups(['read:Post:item'])]
    public ?string $imageUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Post:item'])]
    private ?string $content = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Post:item'])]
    private bool $published = false;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Post:item'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Post:item'])]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['read:Post:item'])]
    private ?\DateTimeInterface $published_at = null;

    #[ORM\OneToMany(targetEntity: PostComment::class, mappedBy: 'post')]
    private Collection $postComments;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'posts')]
    private Collection $categories;

    #[ORM\ManyToMany(targetEntity: Tag::class, mappedBy: 'posts')]
    private Collection $tags;

    public function __construct()
    {
        $this->postComments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->setCreatedAt(new \DateTimeImmutable());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $contentUrl): void
    {
        $this->imageUrl = $contentUrl;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getPublished(): ?bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;
        return $this;
    }

    /**
     * @return Collection<int, PostComment>
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }

    public function addPostComment(PostComment $postComment): self
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments[] = $postComment;
            $postComment->setPost($this);
        }
        return $this;
    }

    public function removePostComment(PostComment $postComment): self
    {
        if ($this->postComments->removeElement($postComment)) {
            if ($postComment->getPost() === $this) {
                $postComment->setPost(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addPost($this);
        }
        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removePost($this);
        }
        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->addPost($this);
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->removeElement($tag)) {
            $tag->removePost($this);
        }
        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\EmptyController;
use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @Vich\Uploadable()
 */
#[ApiResource(
    collectionOperations: [
        "post" => [
            "controller" => EmptyController::class,
            "normalization_context" => ["groups" => ["read:Post:item"]],
            "openapi_context" => [
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
        ],
        "get" => [
            "normalization_context" => ["groups" => ["read:Post:item"]],
        ],
    ],
    itemOperations: [
        "delete",
        "get" => [
            "normalization_context" => ["groups" => ["read:Post:item"]],
        ],
        "put" => [
            "controller" => EmptyController::class,
            "normalization_context" => ["groups" => ["read:Post:item"]],
            "openapi_context" => [
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
        ]
    ],
    attributes: [
        "order"=>[
            "created_at" => "DESC"
        ]
    ]
)]
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:Post:item'])]
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    #[Groups(['read:Post:item'])]
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:Post:item'])]
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:Post:item'])]
    private $summary;

    /**
     * @Vich\UploadableField(mapping="post_images", fileNameProperty="imageName")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $imageName;


    /**
     * @var string|null
     */
    #[Groups(['read:Post:item'])]
    public $imageUrl;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    #[Groups(['read:Post:item'])]
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    #[Groups(['read:Post:item'])]
    private $published;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:Post:item'])]
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:Post:item'])]
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    #[Groups(['read:Post:item'])]
    private $published_at;

    /**
     * @ORM\OneToMany(targetEntity=PostComment::class, mappedBy="post")
     */
    private $postComments;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="posts")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, mappedBy="posts")
     */
    private $tags;

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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return string|null
     */
    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    /**
     * @param string|null $contentUrl
     */
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
     * @return Collection|PostComment[]
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
            // set the owning side to null (unless already changed)
            if ($postComment->getPost() === $this) {
                $postComment->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
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
     * @return Collection|Tag[]
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

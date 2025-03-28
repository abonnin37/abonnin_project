<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\PostCommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PostCommentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Post(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: ['security' => [['bearerAuth' => []]]]
        ),
        new Get(),
        new Put(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: ['security' => [['bearerAuth' => []]]]
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: ['security' => [['bearerAuth' => []]]]
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: ['security' => [['bearerAuth' => []]]]
        )
    ]
)]
class PostComment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:PostComment:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'postComments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:PostComment:item'])]
    private ?Post $post = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'postComments')]
    #[Groups(['read:PostComment:item'])]
    private ?self $parent = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
    #[Groups(['read:PostComment:item'])]
    private Collection $postComments;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:PostComment:item'])]
    private ?string $content = null;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:PostComment:item'])]
    private bool $published = false;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:PostComment:item'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(['read:PostComment:item'])]
    private ?\DateTimeInterface $published_at = null;

    public function __construct()
    {
        $this->postComments = new ArrayCollection();
        $this->created_at = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Post
    {
        return $this->post;
    }

    public function setPost(?Post $post): self
    {
        $this->post = $post;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }

    public function addPostComment(self $postComment): self
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments[] = $postComment;
            $postComment->setParent($this);
        }
        return $this;
    }

    public function removePostComment(self $postComment): self
    {
        if ($this->postComments->removeElement($postComment)) {
            if ($postComment->getParent() === $this) {
                $postComment->setParent(null);
            }
        }
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getPublished(): bool
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

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->published_at;
    }

    public function setPublishedAt(?\DateTimeInterface $published_at): self
    {
        $this->published_at = $published_at;
        return $this;
    }
}

<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post as ApiPost;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new ApiPost(
            openapiContext: ['security' => [['bearerAuth' => []]]],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(),
        new Put(
            openapiContext: ['security' => [['bearerAuth' => []]]],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Patch(
            openapiContext: ['security' => [['bearerAuth' => []]]],
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Delete(
            openapiContext: ['security' => [['bearerAuth' => []]]],
            security: "is_granted('ROLE_ADMIN')"
        )
    ]
)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:Category:item'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'categories')]
    #[Groups(['read:Category:item'])]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    #[Groups(['read:Category:item'])]
    private Collection $categories;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Category:item'])]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Groups(['read:Category:item'])]
    private ?string $summary = null;

    #[ORM\ManyToMany(targetEntity: \App\Entity\Post::class, inversedBy: 'categories')]
    #[Groups(['read:Category:item'])]
    private Collection $posts;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->setParent($this);
        }
        return $this;
    }

    public function removeCategory(self $category): self
    {
        if ($this->categories->removeElement($category)) {
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        }
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
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
        }
        return $this;
    }

    public function removePost(Post $post): self
    {
        $this->posts->removeElement($post);
        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\TorrentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=TorrentRepository::class)
 * @OA\Schema()
 */
class Torrent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="torrent")
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/Comment"))
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="torrents")
     * @OA\Property(type="object", ref="#/components/schemas/User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="torrents")
     * @OA\Property(type="object", ref="#/components/schemas/Category")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @OA\Property(type="string")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string")
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @OA\Property(type="string", format="date-time")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     */
    private $seeders;

    /**
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     */
    private $leechers;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string")
     */
    private $hash;

    /**
     * @ORM\Column(type="text")
     * @OA\Property(type="string")
     */
    private $magnet;

    /**
     * @ORM\Column(type="simple_array")
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    private $files = [];

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @OA\Property(type="array", @OA\Items(type="string"))
     */
    private $trackers = [];

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTorrent($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTorrent() === $this) {
                $comment->setTorrent(null);
            }
        }

        return $this;
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSeeders(): ?int
    {
        return $this->seeders;
    }

    public function setSeeders(int $seeders): self
    {
        $this->seeders = $seeders;

        return $this;
    }

    public function getLeechers(): ?int
    {
        return $this->leechers;
    }

    public function setLeechers(int $leechers): self
    {
        $this->leechers = $leechers;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getMagnet(): ?string
    {
        return $this->magnet;
    }

    public function setMagnet(string $magnet): self
    {
        $this->magnet = $magnet;

        return $this;
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getTrackers(): ?array
    {
        return $this->trackers;
    }

    public function setTrackers(?array $trackers): self
    {
        $this->trackers = $trackers;

        return $this;
    }
}

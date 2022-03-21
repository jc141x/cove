<?php

namespace App\Entity;

use App\Repository\TorrentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=TorrentRepository::class)
 * @OA\Schema(
 * schema="_Torrent",
 * description="includes private fields",
 * )
 * @OA\Schema(
 * schema="Torrent",
 *     @OA\Property(property="id", ref="#/components/schemas/_Torrent/properties/id"),
 *     @OA\Property(property="name", ref="#/components/schemas/_Torrent/properties/name"),
 *     @OA\Property(property="size", ref="#/components/schemas/_Torrent/properties/size"),
 *     @OA\Property(property="date", ref="#/components/schemas/_Torrent/properties/date"),
 *     @OA\Property(property="seeders", ref="#/components/schemas/_Torrent/properties/seeders"),
 *     @OA\Property(property="leechers", ref="#/components/schemas/_Torrent/properties/leechers"),
 *     @OA\Property(property="category", ref="#/components/schemas/Category"),
 *     @OA\Property(property="user", ref="#/components/schemas/User"),
 *     @OA\Property(property="comments", type="array", @OA\Items(ref="#/components/schemas/Comment")),
 *     @OA\Property(property="description", ref="#/components/schemas/_Torrent/properties/description"),
 *     @OA\Property(property="files", type="array", @OA\Items(ref="#/components/schemas/_Torrent/properties/files")),
 *     @OA\Property(property="hash", ref="#/components/schemas/_Torrent/properties/hash"),
 *     @OA\Property(property="magnet", ref="#/components/schemas/_Torrent/properties/magnet"),
 *     @OA\Property(property="trackers", ref="#/components/schemas/_Torrent/properties/trackers"),
 * )
 * 
 */
class Torrent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer", example=1)
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
     * @OA\Property(type="string", example="Torrent name")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @OA\Property(type="string", example="Torrent description")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", example="10.0 GB")
     */
    private $size;

    /**
     * @ORM\Column(type="datetime")
     * @OA\Property(type="string", format="date-time", example="2020-01-01T00:00:00+00:00")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer", example=1)
     */
    private $seeders;

    /**
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer", example=1)
     */
    private $leechers;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", example="785c4c9777b8a342d57fcf60af3b1329")
     */
    private $hash;

    /**
     * @ORM\Column(type="text")
     * @OA\Property(type="string", example="magnet:?xt=urn:btih:785c4c9777b8a342d57fcf60af3b1329")
     */
    private $magnet;

    /**
     * @ORM\Column(type="simple_array")
     * @OA\Property(type="array", @OA\Items(type="string", example="file1.txt"))
     */
    private $files = [];

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @OA\Property(type="array", @OA\Items(type="string", example="tracker1.com"))
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

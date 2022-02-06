<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
/**
 * @ORM\Entity(repositoryClass=CommentRepository::class)
 * @OA\Schema()
 */
class Comment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="comments")
     * @OA\Property(type="object", ref="#/components/schemas/User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Torrent::class, inversedBy="comments")
     * @OA\Property(ref="#/components/schemas/Torrent/properties/id")
     */
    private $torrent;

    /**
     * @ORM\Column(type="datetime")
     * @OA\Property(type="string", format="date-time")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @OA\Property(type="string")
     */
    private $text;

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

    public function getTorrent(): ?Torrent
    {
        return $this->torrent;
    }

    public function setTorrent(?Torrent $torrent): self
    {
        $this->torrent = $torrent;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }
}

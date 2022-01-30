<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Torrent::class, mappedBy="category")
     */
    private $torrents;

    public function __construct()
    {
        $this->torrents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Torrent[]
     */
    public function getTorrents(): Collection
    {
        return $this->torrents;
    }

    public function addTorrent(Torrent $torrent): self
    {
        if (!$this->torrents->contains($torrent)) {
            $this->torrents[] = $torrent;
            $torrent->setCategory($this);
        }

        return $this;
    }

    public function removeTorrent(Torrent $torrent): self
    {
        if ($this->torrents->removeElement($torrent)) {
            // set the owning side to null (unless already changed)
            if ($torrent->getCategory() === $this) {
                $torrent->setCategory(null);
            }
        }

        return $this;
    }
}

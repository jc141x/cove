<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @OA\Schema(schema="_Category", description="includes private fields")
 * @OA\Schema(
 * schema="Category",
 * @OA\Property(property="id", ref="#/components/schemas/_Category/properties/id"),
 * @OA\Property(property="name", ref="#/components/schemas/_Category/properties/id"),)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer", example=1)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", example="Games")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Torrent::class, mappedBy="category")
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/Torrent"))
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

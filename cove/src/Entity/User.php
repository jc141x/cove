<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Annotations as OA;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @OA\Schema(
 * schema="_User",
 * description="includes private fields",)
 * @OA\Schema(
 *  schema="User",
 *  @OA\Property(property="id", ref="#/components/schemas/_User/properties/id"),
 *  @OA\Property(property="username", ref="#/components/schemas/_User/properties/username"),
 * )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer", example=1)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @OA\Property(type="string", example="johndoe")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @OA\Property(type="array", @OA\Items(type="string", example="ROLE_USER"))
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @OA\Property(type="string", format="password", example="$2y$13$fzP9A8pYkBlFG84", description="password is hashed")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @OA\Property(type="string", example="johndoe@email.com")
     */
    private $email;

    /**
     * @ORM\Column(type="integer")
     * @OA\Property(type="integer", deprecated=true)
     */
    private $uploaderstatus;

    /**
     * @ORM\Column(type="datetime")
     * @OA\Property(type="string", format="date-time", example="2020-01-01T00:00:00+00:00")
     */
    private $regdate;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/Comment"))
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Torrent::class, mappedBy="user")
     * @OA\Property(type="array", @OA\Items(type="object", ref="#/components/schemas/Torrent"))
     */
    private $torrents;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->torrents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getUploaderstatus(): ?int
    {
        return $this->uploaderstatus;
    }

    public function setUploaderstatus(int $uploaderstatus): self
    {
        $this->uploaderstatus = $uploaderstatus;

        return $this;
    }

    public function getRegdate(): ?\DateTimeInterface
    {
        return $this->regdate;
    }

    public function setRegdate(\DateTimeInterface $regdate): self
    {
        $this->regdate = $regdate;

        return $this;
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

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
            $torrent->setUser($this);
        }

        return $this;
    }

    public function removeTorrent(Torrent $torrent): self
    {
        if ($this->torrents->removeElement($torrent)) {
            // set the owning side to null (unless already changed)
            if ($torrent->getUser() === $this) {
                $torrent->setUser(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Post::class, orphanRemoval: true)]
    private Collection $posts;

    #[ORM\Column(length: 50)]
    private ?string $username = null;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'friends')]
    private Collection $friends;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastConnectedAt = null;

    #[ORM\OneToMany(mappedBy: 'emitter', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $sendedInvitations;

    #[ORM\OneToMany(mappedBy: 'receiver', targetEntity: Invitation::class, orphanRemoval: true)]
    private Collection $receivedInvitations;

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'likes')]
    private Collection $liked;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->friends = new ArrayCollection();
        $this->lastConnectedAt = new \DateTimeImmutable();
        $this->sendedInvitations = new ArrayCollection();
        $this->receivedInvitations = new ArrayCollection();
        $this->liked = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRole(): string
    {
        if(in_array('ROLE_ADMIN', $this->roles)) return 'Admin';
        if(in_array('ROLE_MODERATOR', $this->roles)) return 'Moderator';
        if(in_array('ROLE_EDITOR', $this->roles)) return 'Editor';
        return 'User';
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

    public function setRoles(array $roles): static
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function __toString(): string
    {
        return $this->username;
    }

    /**
     * @return Collection<int, Post>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): static
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getLastConnectedAt(): ?\DateTimeImmutable
    {
        return $this->lastConnectedAt;
    }

    public function setLastConnectedAt(\DateTimeImmutable $lastConnectedAt): static
    {
        $this->lastConnectedAt = $lastConnectedAt;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFriends(): Collection
    {
        return $this->friends;
    }

    public function addFriend(self $friend): static
    {
        if (!$this->friends->contains($friend)) {
            $this->friends->add($friend);
        }

        return $this;
    }

    public function removeFriend(self $friend): static
    {
        $this->friends->removeElement($friend);

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getSendedInvitations(): Collection
    {
        return $this->sendedInvitations;
    }

    public function addSendedInvitation(Invitation $sendedInvitation): static
    {
        if (!$this->sendedInvitations->contains($sendedInvitation)) {
            $this->sendedInvitations->add($sendedInvitation);
            $sendedInvitation->setEmitter($this);
        }

        return $this;
    }

    public function removeSendedInvitation(Invitation $sendedInvitation): static
    {
        if ($this->sendedInvitations->removeElement($sendedInvitation)) {
            // set the owning side to null (unless already changed)
            if ($sendedInvitation->getEmitter() === $this) {
                $sendedInvitation->setEmitter(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Invitation>
     */
    public function getReceivedInvitations(): Collection
    {
        return $this->receivedInvitations;
    }

    public function addReceivedInvitation(Invitation $receivedInvitation): static
    {
        if (!$this->receivedInvitations->contains($receivedInvitation)) {
            $this->receivedInvitations->add($receivedInvitation);
            $receivedInvitation->setReceiver($this);
        }

        return $this;
    }

    public function removeReceivedInvitation(Invitation $receivedInvitation): static
    {
        if ($this->receivedInvitations->removeElement($receivedInvitation)) {
            // set the owning side to null (unless already changed)
            if ($receivedInvitation->getReceiver() === $this) {
                $receivedInvitation->setReceiver(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Post>
     */
    public function getLiked(): Collection
    {
        return $this->liked;
    }

    public function addLiked(Post $liked): static
    {
        if (!$this->liked->contains($liked)) {
            $this->liked->add($liked);
            $liked->addLike($this);
        }

        return $this;
    }

    public function removeLiked(Post $liked): static
    {
        if ($this->liked->removeElement($liked)) {
            $liked->removeLike($this);
        }

        return $this;
    }
}

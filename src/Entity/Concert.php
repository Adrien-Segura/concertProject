<?php

namespace App\Entity;

use App\Repository\ConcertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConcertRepository::class)
 */
class Concert
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
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Room::class, inversedBy="concerts")
     */
    private $room;

    /**
     * @ORM\ManyToMany(targetEntity=Member::class, inversedBy="concerts")
     */
    private $members;

    /**
     * @ORM\ManyToMany(targetEntity=Band::class, inversedBy="concerts")
     */
    private $bands;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="favoriteConcerts")
     */
    private $users;

    public function __construct()
    {
        $this->bands = new ArrayCollection();
        $this->members = new ArrayCollection();
        $this->favorisConcerts = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    /**
     * @return Collection|member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(member $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    /**
     * @return Collection|band[]
     */
    public function getBands(): Collection
    {
        return $this->bands;
    }

    public function addBand(band $band): self
    {
        if (!$this->bands->contains($band)) {
            $this->bands[] = $band;
        }

        return $this;
    }

    public function removeBand(band $band): self
    {
        $this->bands->removeElement($band);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addFavoriteConcert($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeFavoriteConcert($this);
        }

        return $this;
    }
}

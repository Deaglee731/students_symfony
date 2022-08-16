<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 25)]
    private $name;

    #[ORM\Column(type: 'string', length: 25)]
    private $first_name;

    #[ORM\Column(type: 'string', length: 25)]
    private $last_name;

    #[ORM\Column(type: 'string', length: 40)]
    private $email;

    #[ORM\Column(type: 'date')]
    private $birthday;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private $groups;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $avatar;

    #[ORM\OneToMany(targetEntity: Subject::class, mappedBy: "user")]
    private $subjects;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Score::class)]
    private $scores;

    #[ORM\Column(type: 'json', nullable: true)]
    private $address = [];

    public const COLOR_GREEN = 'green';
    public const COLOR_YELLOW = 'yellow';
    public const COLOR_RED = 'red';

    public function __construct()
    {
        $this->scores = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * @param mixed $subjects
     */
    public function setSubjects($subjects): void
    {
        $this->subjects = $subjects;
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

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
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

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getGroups(): ?Group
    {
        return $this->groups;
    }

    public function setGroups(?Group $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getFullName()
    {
        return $this->getName() . " " . $this->getFirstName();
    }

    public function __toString(): string
    {
        return (string)$this->getFullName();
    }

    /**
     * @return Collection<int, Score>
     */
    public function getScores(): Collection
    {
        return $this->scores;
    }

    public function addScore(Score $score): self
    {
        if (!$this->scores->contains($score)) {
            $this->scores[] = $score;
            $score->setUser($this);
        }

        return $this;
    }

    public function removeScore(Score $score): self
    {
        if ($this->scores->removeElement($score)) {
            // set the owning side to null (unless already changed)
            if ($score->getUser() === $this) {
                $score->setUser(null);
            }
        }

        return $this;
    }

    public function getColor()
    {
        $scores = new ArrayCollection($this->getScores()->getValues());
        $min = 100;

        $scores->map(function ($value) use (&$min) {
            if ($value->getScore() != null &&$value->getScore() <= $min) {
                return $min = $value->getScore();
            }
        });

        return ($min == 5 ? User::COLOR_GREEN : ($min == 4 ? User::COLOR_YELLOW : User::COLOR_RED));
    }

    public function getAddress(): ?array
    {
        return $this->address;
    }

    public function setAddress(?array $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getFullAddress(): string
    {
        return " Город " . $this->getAddress()['city'] ."\n" . " Улица " . $this->getAddress()['street'] . " Дом ". $this->getAddress()['home'];
    }
}

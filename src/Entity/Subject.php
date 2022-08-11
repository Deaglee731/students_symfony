<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 25)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'subject', targetEntity: Score::class)]
    private $scores;

    /**
     * @return mixed
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * @param mixed $scores
     */
    public function setScores($scores): void
    {
        $this->scores = $scores;
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

    public function __toString(): string
    {
        return $this->id;
    }

    public function getAverageScores()
    {
        $scores = new ArrayCollection($this->getScores()->getValues());
        $sumAllScore = 0;

        $scores->map(function ($value) use (&$sumAllScore){
            $score = $value->getScore();
            $sumAllScore +=$score;
        });

        return count($scores) == 0 ? 0 : $sumAllScore/count($scores);
    }
}

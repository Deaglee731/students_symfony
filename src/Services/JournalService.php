<?php

namespace App\Services;

use App\Entity\Group;
use App\Entity\Score;
use App\Entity\Subject;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

class JournalService
{
    public $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getJournalAllStudents($group)
    {
        $users = $group->getUsers();
        $studentScores = new ArrayCollection();
        $subjects = new ArrayCollection($this->doctrine->getRepository(Subject::class)->findAll());

        foreach ($users as $user) {
            foreach ($subjects as $subject) {
                if (!$user->getScores()->exists(function ($key, Score $score) use ($subject) {
                    return $score->getSubject() === $subject;
                })) {
                    $score = new Score();
                    $score->setSubject($subject);
                    $score->setUser($user);
                    $score->setScore(null);
                    $user->addScore($score);
                }
            }

            $studentScores->add([
                'name' => $user->getFullName(),
                'scores' => $user->getScores()->matching(Criteria::create()->orderBy(['subject' => Criteria::ASC])),
                'color' => $user->getColor()]);
        }

        return $studentScores;
    }

    public function getAvrageScoreForStudents(Group $group)
    {
        $users = new ArrayCollection($group->getUsers()->getValues());
        $subjects = new ArrayCollection($this->doctrine->getRepository(Subject::class)->findAll());
        $avgScore = new ArrayCollection();

        foreach ($subjects as $subject) {
            $users->map(function ($user) use ($subject, &$avgScore) {
                $user->getScores()->map(function ($score) use ($subject, &$avgScore) {
                    if ($score->getSubject() == $subject) {
                        $avgScore[$score->getSubject()->getId()] =
                            array_merge(
                                $avgScore[$score->getSubject()->getId()] ?? [],
                                [$score->getScore() ?? []]);
                    }
                });
            });
        }

        $avgScore = $avgScore->map(function ($subject) {
            $subject = array_filter($subject);

            return count($subject) == 0 ? 0 : array_sum($subject)/count($subject);
        });

        return $avgScore;
    }
}
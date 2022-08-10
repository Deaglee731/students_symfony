<?php

namespace App\Services;

use App\Entity\Score;
use App\Entity\Subject;
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
        $scores = new ArrayCollection();
        $subjects = new ArrayCollection($this->doctrine->getRepository(Subject::class)->findAll());

        foreach ($users as $user)
        {
            foreach ($subjects as $subject)
            {
                if(!$user->getScores()->exists(function ($key, Score $score) use ($subject){
                    return $score->getSubject()?->getId() == $subject->getId();
                }))
                {
                    $score = new Score();
                    $score->setSubject($subject);
                    $score->setUser($user);
                    $score->setScore(null);

                    $user->addScore($score);
                }
            }

            $studentScores->set(
                $user->getFullName(),
                $user->getScores()->matching(Criteria::create()->orderBy(['subject' => Criteria::ASC]))
            );
        }

        return $studentScores;
    }
}
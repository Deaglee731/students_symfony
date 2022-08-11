<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Score;
use App\Entity\Subject;
use App\Entity\User;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Date;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i<5; $i++)
        {
            $group = new Group();
            $group->setName("Group $i");
            $manager->persist($group);
        }
        for ($i = 0; $i<5; $i++)
        {
            $user = new User();
            $user->setGroups($group);
            $user->setName("User name $i");
            $user->setFirstName("User firstname $i");
            $user->setLastName("user lastname $i");
            $user->setBirthday(Carbon::now());
            $user->setEmail("User $i @mail.ru");
            $score = new Score();
            $subject = new Subject();
            $subject->setName("Subject $i");
            $score->setSubject($subject);
            $score->setUser($user);
            $score->setScore($i);
            $manager->persist($user);
            $manager->persist($subject);
            $manager->persist($score);
        }

        $manager->flush();
    }
}

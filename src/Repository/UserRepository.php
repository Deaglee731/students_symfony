<?php

namespace App\Repository;

use App\Entity\Score;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function add(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getBadStudents()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult(User::class, $alias = 'user');
        $rsm->addFieldResult($alias, 'id', 'id');

        $query = $this
            ->_em
            ->createNativeQuery(
                sql: <<<SQL
                    SELECT
                        u.id
                    FROM
                        user u 
                    JOIN score s 
                    ON u.id = s.user_id
                    GROUP BY user_id
                    HAVING MIN(score) <= 3
                    SQL,
                rsm: $rsm
            );

        $result = $query->getResult();

        return $result;
    }

    public function getGoodStudents()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult(User::class, $alias = 'user');
        $rsm->addFieldResult($alias, 'id', 'id');

        $query = $this
            ->_em
            ->createNativeQuery(
                sql: <<<SQL
                    SELECT
                        u.id
                    FROM
                        user u 
                    JOIN score s 
                    ON u.id = s.user_id
                    GROUP BY user_id
                    HAVING MIN(score) = 4
                    SQL,
                rsm: $rsm
            );

        $result = $query->getResult();

        return $result;
    }

    public function getBestStudents()
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult(User::class, $alias = 'user');
        $rsm->addFieldResult($alias, 'id', 'id');

        $query = $this
            ->_em
            ->createNativeQuery(
                sql: <<<SQL
                    SELECT
                        u.id
                    FROM
                        user u 
                    JOIN score s 
                    ON u.id = s.user_id
                    GROUP BY user_id
                    HAVING MIN(score) = 5
                    SQL,
                rsm: $rsm
            );

        $result = $query->getResult();

        return $result;
    }

    public function getUserPaginator(int $offset, $filteredquery): Paginator
    {
        $filteredquery
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($filteredquery);
    }

    public function findByField($request)
    {
        $result = $this->createQueryBuilder('u');

        if ($request->query->has('name')) {
            $result->andWhere("u.name LIKE :name")
                ->setParameter('name', "%" . $request->query->get('name') . "%");
        }
        if ($request->query->has('first_name')) {
            $result->andWhere("u.first_name LIKE :first_name")
                ->setParameter('first_name', "%" . $request->query->get('first_name') . "%");
        }
        if ($request->query->has('last_name')) {
            $result->andWhere("u.last_name LIKE :last_name")
                ->setParameter('last_name', "%" . $request->query->get('last_name') . "%");
        }

        if ($request->query->has('birthday')) {
            $result->andWhere("u.birthday LIKE :birthday")
                ->setParameter('birthday', "%" . $request->query->get('birthday') . "%");
        }

        return $result;
    }




//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

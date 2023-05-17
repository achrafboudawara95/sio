<?php

namespace App\Repository;

use App\Entity\TimeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimeLog>
 *
 * @method TimeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeLog[]    findAll()
 * @method TimeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeLog::class);
    }

    public function save(TimeLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TimeLog $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOverlappingTimeLogs(TimeLog $timeLog): array
    {
        $qb = $this->createQueryBuilder('tl');
        $qb->andWhere($qb->expr()->eq('tl.project', ':project'))
            ->andWhere($qb->expr()->lt('tl.startTime', ':endTime'))
            ->andWhere($qb->expr()->gt('tl.endTime', ':startTime'))
            ->setParameter('project', $timeLog->getProject())
            ->setParameter('startTime', $timeLog->getStartTime())
            ->setParameter('endTime', $timeLog->getEndTime());

        return $qb->getQuery()->getResult();
    }
}

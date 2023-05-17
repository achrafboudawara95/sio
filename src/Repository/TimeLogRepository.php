<?php

namespace App\Repository;

use App\Entity\Project;
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

    /**
     * Search for time logs that are in the same date range of $timeLog
     *
     * @param TimeLog $timeLog
     * @return array
     */
    public function findOverlappingTimeLogs(TimeLog $timeLog): array
    {
        $qb = $this->createQueryBuilder('tl');
        $qb->andWhere($qb->expr()->eq('tl.project', ':project'))
            ->andWhere($qb->expr()->lt('tl.startTime', ':endTime'))
            ->setParameter('project', $timeLog->getProject())
            ->setParameter('endTime', $timeLog->getEndTime());

        if (null !== $timeLog->getEndTime()) {
            $qb->andWhere($qb->expr()->gt('tl.endTime', ':startTime'))
                ->setParameter('startTime', $timeLog->getStartTime());
        }

        return $qb->getQuery()->getResult();
    }

    public function findByProjectWithSortedTimeLogs(Project $project): array
    {
        return $this->createQueryBuilder('tl')
            ->where('tl.project = :project')
            ->setParameter('project', $project)
            ->orderBy('tl.endTime', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

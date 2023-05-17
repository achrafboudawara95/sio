<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 *
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function getProjectsReport(User $user): array
    {
        $qb = $this->createQueryBuilder('p')
            ->leftJoin('p.timeLogs', 'tl')
            ->select('p.name', 'tl.startTime', 'tl.endTime')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('p.id', 'DESC')
            ->addOrderBy('tl.endTime', 'DESC');

        return $qb->getQuery()->getArrayResult();
    }

    public function getWorkDurationsByDay(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DATE(start_time) AS day, SUM(TIMESTAMPDIFF(SECOND, start_time, end_time)) / 3600 AS total_duration_hours
                FROM time_log t
                INNER JOIN project p on p.id = t.project_id
                WHERE start_time BETWEEN :start_time AND :end_time
                AND p.user_id = :user_id
                GROUP BY day";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('start_time', $startDate->format('Y-m-d H:i:s'));
        $stmt->bindValue('end_time', $endDate->format('Y-m-d H:i:s'));
        $stmt->bindValue('user_id', $user->getId());

        return $stmt->executeQuery()->fetchAll();
    }

    public function getWorkDurationsByMonth(User $user, \DateTime $startDate, \DateTime $endDate): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT DATE_FORMAT(start_time, '%Y-%m') AS month, SUM(TIMESTAMPDIFF(SECOND, start_time, end_time)) / 3600 AS total_duration_hours
            FROM time_log t
            INNER JOIN project p on p.id = t.project_id
            WHERE start_time BETWEEN :start_time AND :end_time
            AND p.user_id = :user_id
            GROUP BY month";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue('start_time', $startDate->format('Y-m-d H:i:s'));
        $stmt->bindValue('end_time', $endDate->format('Y-m-d H:i:s'));
        $stmt->bindValue('user_id', $user->getId());

        return $stmt->executeQuery()->fetchAll();
    }
}

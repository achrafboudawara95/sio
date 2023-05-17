<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\TimeLog;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\TimeLogRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class TimeLogService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FlashServiceInterface $flashService,
        private readonly TimeLogRepository $timeLogRepository
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function createTimeLog(Project $project, TimeLog $timeLog): void
    {
        $timeLog->setProject($project);

        $this->verifyOverlapping($timeLog);

        $this->entityManager->persist($timeLog);
        $this->entityManager->flush();

        $this->flashService->addFlashSuccess('Time line created successfully.');
    }

    public function delete(TimeLog $timeLog): void
    {
        $this->entityManager->remove($timeLog);
        $this->entityManager->flush();
    }

    /**
     * @throws ValidationException
     */
    public function start(Project $project): void
    {
        $timeLog = new TimeLog();
        $timeLog->setStartTime(new DateTime());
        $timeLog->setEndTime(null);
        $timeLog->setProject($project);

        $this->verifyOverlapping($timeLog);

        $this->entityManager->persist($timeLog);
        $this->entityManager->flush();
    }

    /**
     * @throws ValidationException
     */
    private function verifyOverlapping(TimeLog $timeLog): void
    {
        if (!empty($this->timeLogRepository->findOverlappingTimeLogs($timeLog)))
        {
            $this->flashService->addFlashError('You can\'t overlap time log.');
            throw new ValidationException("Overlapping timeLog");
        }
    }
}
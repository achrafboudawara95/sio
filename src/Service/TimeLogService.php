<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\TimeLog;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\TimeLogRepository;
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
    public function createTimeLog(Project $project, TimeLog $timeLog,): void
    {
        $timeLog->setProject($project);

        if (!empty($this->timeLogRepository->findOverlappingTimeLogs($timeLog)))
        {

            $this->flashService->addFlashError('You can\'t overlap time log.');
            throw new ValidationException("Overlapping timeLog");
        }

        $this->entityManager->persist($timeLog);
        $this->entityManager->flush();

        $this->flashService->addFlashSuccess('Time line created successfully.');
    }
}
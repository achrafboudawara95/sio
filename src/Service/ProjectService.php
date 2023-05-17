<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Writer;

class ProjectService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectRepository $projectRepository,
        private readonly FlashServiceInterface $flashService
    )
    {
    }

    /**
     * @return []Project
     */
    public function getProjectsByUser(User $user): array
    {
        return $this->projectRepository->findBy(['user' => $user]);
    }

    public function createProject(Project $project, User $user): void
    {
        $project->setUser($user);
        $this->entityManager->persist($project);
        $this->entityManager->flush();

        $this->flashService->addFlashSuccess('Project created successfully.');
    }

    public function getProjectsDailyReport(User $user, DateTime $startDate, DateTime $endDate): array
    {
        return $this->projectRepository->getWorkDurationsByDay($user, $startDate, $endDate);
    }

    public function getProjectsMonthlyReport(User $user, DateTime $startDate, DateTime $endDate): array
    {
        return $this->projectRepository->getWorkDurationsByMonth($user, $startDate, $endDate);
    }

    public function getProjectsReport(User $user): Writer
    {
        $projects = $this->projectRepository->findBy(['user' => $user]);

        // Create a new CSV writer
        $csvWriter = Writer::createFromString('');

        // Set the headers for the CSV
        $headers = ['Project', 'Start Time', 'End Time'];
        $csvWriter->insertOne($headers);

        // Add the data rows to the CSV
        foreach ($projects as $project) {
            foreach ($project->getTimeLogs() as $timeLog) {
                $data = [
                    $project->getName(),
                    $timeLog->getStartTime()->format('Y-m-d H:i:s'),
                    $timeLog->getEndTime()->format('Y-m-d H:i:s')
                ];
                $csvWriter->insertOne($data);
            }
        }

        return $csvWriter;
    }
}
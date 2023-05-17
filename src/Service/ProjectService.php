<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\User;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;

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
}